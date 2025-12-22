<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TrainingContent;
use App\Models\ContentModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContentManagerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

            $query = Booking::with([
                'organization:org_id,name,email,org_image',
                'requirement.program:program_id,title,duration,program_type_id'
            ])
                ->where('trainer_id', $trainerId)
                ->whereHas('requirement', function ($q) {
                    // Not rejected or completed? Depending on logic. 
                    // Showing all for now or filtering by status logic if exists.
                });

            // Search Filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('requirement.program', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
                    })
                        ->orWhereHas('organization', function ($oq) use ($search) {
                            $oq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Mode Filter (from requirement)
            if ($request->has('filter_mode') && !empty($request->filter_mode)) {
                $mode = $request->filter_mode;
                $query->whereHas('requirement', function ($q) use ($mode) {
                    $q->where('mode', $mode);
                });
            }

            $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

            // Append content count to each booking
            $bookings->getCollection()->transform(function ($booking) {
                $booking->content_count = TrainingContent::where('booking_id', $booking->booking_id)->count();
                return $booking;
            });

            return response()->json([
                'status' => true,
                'html' => view('trainer.content-manager.partials.booking-list', compact('bookings'))->render(),
                'pagination' => $bookings->links('pagination::bootstrap-5')->toHtml()
            ]);
        }

        return view('trainer.content-manager.index');
    }

    public function manage($booking_id)
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        $booking = Booking::with([
            'organization:org_id,name,email,org_image',
            'requirement.program:program_id,title,duration'
        ])
            ->where('booking_id', $booking_id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        $contents = TrainingContent::where('booking_id', $booking_id)
            ->orderBy('created_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trainer.content-manager.manage', compact('booking', 'contents'));
    }

    public function add($booking_id)
    {
        // Verify booking belongs to trainer
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        $booking = Booking::where('booking_id', $booking_id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        return view('trainer.content-manager.add-content', compact('booking_id'));
    }

    public function edit($content_id)
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        $content = TrainingContent::where('content_id', $content_id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        $booking_id = $content->booking_id;

        return view('trainer.content-manager.edit-content', compact('content', 'booking_id'));
    }

    public function update(Request $request)
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;
        $contentId = $request->content_id;

        $content = TrainingContent::where('content_id', $contentId)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Rules - similar to store but file optional
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];

        // Check validation rules based on selected content type (allow changing type)
        $contentType = $request->content_type ?? $content->content_type;
        $rules['content_type'] = 'required|in:video,text,pdf,link,meeting';

        if ($contentType == 'text') {
            $rules['text_content'] = 'required|string';
        }
        if ($contentType == 'link') {
            $rules['external_url'] = 'required|url|max:500';
        }
        if ($contentType == 'meeting') {
            $rules['meeting_url'] = 'required|url|max:500';
        }
        // File validation only if new file uploaded or changing to file type from non-file type without file?
        // If type remains same, file is optional. If type changes to file type, file is required.
        if ($contentType == 'video') {
            if ($request->hasFile('video_file')) {
                $rules['video_file'] = 'required|file|mimes:mp4,avi,mov,wmv|max:102400';
            } elseif ($content->content_type != 'video' && !$content->file_path) {
                // Changed to video but no file? Error.
                $rules['video_file'] = 'required|file|mimes:mp4,avi,mov,wmv|max:102400';
            }
        }
        if ($contentType == 'pdf') {
            if ($request->hasFile('pdf_file')) {
                $rules['pdf_file'] = 'required|file|mimes:pdf|max:25600';
            } elseif ($content->content_type != 'pdf' && !$content->file_path) {
                $rules['pdf_file'] = 'required|file|mimes:pdf|max:25600';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $content->title = $request->title;
            $content->description = $request->description;
            $content->content_type = $contentType; // Update type
            $content->module_id = null; // Ensure null if column exists
            $content->is_visible_to_org = $request->has('is_visible_to_org') ? 1 : 0;
            $content->is_visible_to_candidates = $request->has('is_visible_to_candidates') ? 1 : 0;

            if ($contentType == 'text') {
                $content->text_content = $request->text_content;
                // Clear file/url if changing type? Optional cleanup.
                $content->external_url = null;
            } elseif ($contentType == 'link') {
                $content->external_url = $request->external_url;
                $content->text_content = null;
            } elseif ($contentType == 'meeting') {
                $content->external_url = $request->input('meeting_url');
                $content->text_content = null;
            } elseif ($contentType == 'video') {
                if ($request->hasFile('video_file')) {
                    // Delete old if exists
                    if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                        Storage::disk('public')->delete($content->file_path);
                    }
                    $path = $request->file('video_file')->store('training_content/videos/' . $content->booking_id, 'public');
                    $content->file_path = $path;
                }
                $content->text_content = null;
                $content->external_url = null;
            } elseif ($contentType == 'pdf') {
                if ($request->hasFile('pdf_file')) {
                    if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                        Storage::disk('public')->delete($content->file_path);
                    }
                    $path = $request->file('pdf_file')->store('training_content/pdfs/' . $content->booking_id, 'public');
                    $content->file_path = $path;
                }
                $content->text_content = null;
                $content->external_url = null;
            }

            $content->save();

            return response()->json([
                'status' => true,
                'message' => 'Content updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update content: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getBookingDetails($booking_id)
    {
        try {
            $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

            $booking = Booking::with([
                'organization:org_id,name,email,org_image',
                'requirement' => function ($query) {
                    $query->with('program:program_id,title,duration');
                }
            ])
                ->where('booking_id', $booking_id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            $contentCount = TrainingContent::where('booking_id', $booking_id)->count();

            return response()->json([
                'status' => true,
                'data' => [
                    'organization' => $booking->organization,
                    'requirement' => $booking->requirement,
                    'content_count' => $contentCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to load booking details'
            ], 500);
        }
    }



    public function store(Request $request)
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;
        $bookingId = $request->booking_id;

        // Verify booking belongs to trainer
        $booking = Booking::where('booking_id', $bookingId)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Get the training mode from requirement
        $mode = $booking->requirement->mode;

        // Common validation
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'content_types' => 'required|array|min:1',
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $createdContents = [];
        $errors = [];

        foreach ($request->content_types as $type) {
            // Per-type rules
            $rules = [];
            $messages = [];

            if ($type == 'video') {
                $rules['video_file'] = 'required|file|mimes:mp4,avi,mov,wmv|max:102400';
                $messages['video_file.required'] = 'Video file is required when Video type is selected.';
            } elseif ($type == 'text') {
                $rules['text_content'] = 'required|string';
                $messages['text_content.required'] = 'Text content is required when Text type is selected.';
            } elseif ($type == 'pdf') {
                $rules['pdf_file'] = 'required|file|mimes:pdf|max:25600';
                $messages['pdf_file.required'] = 'PDF file is required when PDF type is selected.';
            } elseif ($type == 'link') {
                $rules['external_url'] = 'required|url|max:500';
                $messages['external_url.required'] = 'URL is required when Link type is selected.';
            } elseif ($type == 'meeting') {
                $rules['meeting_url'] = 'required|url|max:500';
                $messages['meeting_url.required'] = 'Meeting URL is required when Meeting type is selected.';
            }

            if (!empty($rules)) {
                $typeValidator = Validator::make($request->all(), $rules, $messages);
                if ($typeValidator->fails()) {
                    // Accumulate errors
                    foreach ($typeValidator->errors()->all() as $error) {
                        $errors[] = $error;
                    }
                    continue; // Skip creating this content
                }
            }

            try {
                $data = [
                    'booking_id' => $bookingId,
                    'trainer_id' => $trainerId,
                    'module_id' => null,
                    'mode' => $mode,
                    'content_type' => $type,
                    'title' => $request->title,
                    'description' => $request->description,
                    'is_visible_to_org' => $request->has('is_visible_to_org') ? 1 : 0,
                    'is_visible_to_candidates' => $request->has('is_visible_to_candidates') ? 1 : 0,
                ];

                // Handle file/content
                if ($type == 'video' && $request->hasFile('video_file')) {
                    $data['file_path'] = $request->file('video_file')->store('training_content/videos/' . $bookingId, 'public');
                } elseif ($type == 'text') {
                    $data['text_content'] = $request->text_content;
                } elseif ($type == 'pdf' && $request->hasFile('pdf_file')) {
                    $data['file_path'] = $request->file('pdf_file')->store('training_content/pdfs/' . $bookingId, 'public');
                } elseif ($type == 'link') {
                    $data['external_url'] = $request->external_url;
                } elseif ($type == 'meeting') {
                    $data['external_url'] = $request->meeting_url;
                }

                $createdContents[] = TrainingContent::create($data);

            } catch (\Exception $e) {
                $errors[] = "Failed to save $type: " . $e->getMessage();
            }
        }

        if (count($errors) > 0 && count($createdContents) == 0) {
            // All failed
            return response()->json(['status' => false, 'message' => 'Failed to add content due to validation or server errors.', 'errors' => $errors], 422);
        }

        return response()->json([
            'status' => true,
            'message' => count($createdContents) . ' content items added successfully!' . (count($errors) > 0 ? ' Some items failed.' : ''),
            'warnings' => $errors,
            'data' => $createdContents
        ]);
    }

    public function destroy($id)
    {
        try {
            $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

            $content = TrainingContent::where('content_id', $id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            // Delete file if exists
            if ($content->file_path && Storage::disk('public')->exists($content->file_path)) {
                Storage::disk('public')->delete($content->file_path);
            }

            $content->delete();

            return response()->json([
                'status' => true,
                'message' => 'Content deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete content'
            ], 500);
        }
    }
}