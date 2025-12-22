<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TrainingContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContentManagerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $trainerId = Auth::user()->trainer_id;

            $query = Booking::with([
                'organization:org_id,name,email,org_image',
                'requirement.program:program_id,title,duration,program_type_id'
            ])
                ->where('trainer_id', $trainerId);

            // Search Filter
            if ($request->filled('search')) {
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

            // Mode Filter
            if ($request->filled('filter_mode')) {
                $mode = $request->filter_mode;
                $query->whereHas('requirement', function ($q) use ($mode) {
                    $q->where('mode', $mode);
                });
            }

            $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

            // Append content count
            $bookings->getCollection()->transform(function ($booking) {
                $booking->content_count = TrainingContent::where('booking_id', $booking->booking_id)->count();
                return $booking;
            });

            return response()->json([
                'success' => true,
                'message' => 'Trainings for content management fetched successfully',
                'data' => $bookings,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trainings: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function manage($booking_id)
    {
        try {
            $trainerId = Auth::user()->trainer_id;

            $booking = Booking::with([
                'organization:org_id,name,email,org_image',
                'requirement.program:program_id,title,duration'
            ])
                ->where('booking_id', $booking_id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            $contents = TrainingContent::where('booking_id', $booking_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Training content details fetched successfully',
                'data' => [
                    'booking' => $booking,
                    'contents' => $contents
                ],
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found or access denied',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }

    public function getBookingDetails($booking_id)
    {
        try {
            $trainerId = Auth::user()->trainer_id;

            $booking = Booking::with([
                'organization:org_id,name,email,org_image',
                'requirement.program:program_id,title,duration'
            ])
                ->where('booking_id', $booking_id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            $contentCount = TrainingContent::where('booking_id', $booking_id)->count();

            return response()->json([
                'success' => true,
                'message' => 'Booking details fetched successfully',
                'data' => [
                    'organization' => $booking->organization,
                    'requirement' => $booking->requirement,
                    'content_count' => $contentCount
                ],
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $trainerId = Auth::user()->trainer_id;
            $bookingId = $request->booking_id;

            $booking = Booking::with('requirement')->where('booking_id', $bookingId)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|exists:bookings,booking_id',
                'content_types' => 'required|array|min:1',
                'title' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'data' => $validator->errors(),
                    'status' => 422
                ], 422);
            }

            $createdContents = [];
            $errors = [];

            foreach ($request->content_types as $type) {
                $rules = [];
                if ($type == 'video')
                    $rules['video_file'] = 'required|file|mimes:mp4,avi,mov,wmv|max:102400';
                elseif ($type == 'text')
                    $rules['text_content'] = 'required|string';
                elseif ($type == 'pdf')
                    $rules['pdf_file'] = 'required|file|mimes:pdf|max:25600';
                elseif ($type == 'link')
                    $rules['external_url'] = 'required|url|max:500';
                elseif ($type == 'meeting')
                    $rules['meeting_url'] = 'required|url|max:500';

                if (!empty($rules)) {
                    $typeValidator = Validator::make($request->all(), $rules);
                    if ($typeValidator->fails()) {
                        $errors[$type] = $typeValidator->errors()->all();
                        continue;
                    }
                }

                $data = [
                    'booking_id' => $bookingId,
                    'trainer_id' => $trainerId,
                    'mode' => $booking->requirement->mode,
                    'content_type' => $type,
                    'title' => $request->title,
                    'description' => $request->description,
                    'is_visible_to_org' => $request->has('is_visible_to_org') ? 1 : 0,
                    'is_visible_to_candidates' => $request->has('is_visible_to_candidates') ? 1 : 0,
                ];

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
            }

            if (empty($createdContents) && !empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add content items',
                    'data' => $errors,
                    'status' => 422
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => count($createdContents) . ' content items added successfully',
                'data' => [
                    'created' => $createdContents,
                    'errors' => $errors
                ],
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $trainerId = Auth::user()->trainer_id;
            $content = TrainingContent::where('content_id', $id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            $rules = [
                'title' => 'required|string|max:255',
                'content_type' => 'required|in:video,text,pdf,link,meeting',
            ];

            $type = $request->content_type ?? $content->content_type;
            if ($type == 'text')
                $rules['text_content'] = 'required|string';
            elseif ($type == 'link')
                $rules['external_url'] = 'required|url|max:500';
            elseif ($type == 'meeting')
                $rules['meeting_url'] = 'required|url|max:500';

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'data' => $validator->errors(),
                    'status' => 422
                ], 422);
            }

            $content->title = $request->title;
            $content->description = $request->description;
            $content->content_type = $type;
            $content->is_visible_to_org = $request->has('is_visible_to_org') ? 1 : 0;
            $content->is_visible_to_candidates = $request->has('is_visible_to_candidates') ? 1 : 0;

            if ($type == 'text') {
                $content->text_content = $request->text_content;
                $content->external_url = null;
            } elseif ($type == 'link') {
                $content->external_url = $request->external_url;
                $content->text_content = null;
            } elseif ($type == 'meeting') {
                $content->external_url = $request->meeting_url;
                $content->text_content = null;
            } elseif ($type == 'video' && $request->hasFile('video_file')) {
                if ($content->file_path)
                    Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('video_file')->store('training_content/videos/' . $content->booking_id, 'public');
            } elseif ($type == 'pdf' && $request->hasFile('pdf_file')) {
                if ($content->file_path)
                    Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('pdf_file')->store('training_content/pdfs/' . $content->booking_id, 'public');
            }

            $content->save();

            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully',
                'data' => $content,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update content: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $trainerId = Auth::user()->trainer_id;
            $content = TrainingContent::where('content_id', $id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            if ($content->file_path)
                Storage::disk('public')->delete($content->file_path);
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully',
                'data' => null,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete content',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
