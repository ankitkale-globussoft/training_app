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
    public function index()
    {
        return view('trainer.content-manager.index');
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

    public function getBookingDetails($booking_id)
    {
        try {
            $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

            $booking = Booking::with([
                'organization:org_id,name,email,org_image',
                'requirement' => function($query) {
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

    public function getModules($booking_id)
    {
        try {
            $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

            // Verify booking belongs to trainer
            Booking::where('booking_id', $booking_id)
                ->where('trainer_id', $trainerId)
                ->firstOrFail();

            $modules = ContentModule::where('booking_id', $booking_id)
                ->orderBy('module_order')
                ->get(['module_id', 'module_name']);

            return response()->json([
                'status' => true,
                'data' => $modules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => []
            ]);
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

        // Define validation rules
        $rules = [
            'booking_id' => 'required|exists:bookings,booking_id',
            'content_type' => 'required|in:video,text,pdf,link,meeting',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:content_modules,module_id',
            'is_visible_to_org' => 'boolean',
            'is_visible_to_candidates' => 'boolean',
        ];

        $messages = [
            'booking_id.required' => 'Booking ID is required',
            'content_type.required' => 'Please select a content type',
            'title.required' => 'Content title is required',
            'title.max' => 'Title cannot exceed 255 characters',
        ];

        // Add content type specific validation
        switch ($request->content_type) {
            case 'video':
                $rules['video_file'] = 'required|file|mimes:mp4,avi,mov,wmv|max:102400'; // 100MB
                $messages['video_file.required'] = 'Please upload a video file';
                $messages['video_file.mimes'] = 'Video must be MP4, AVI, MOV, or WMV format';
                $messages['video_file.max'] = 'Video size cannot exceed 100MB';
                break;

            case 'text':
                $rules['text_content'] = 'required|string';
                $messages['text_content.required'] = 'Text content is required';
                break;

            case 'pdf':
                $rules['pdf_file'] = 'required|file|mimes:pdf|max:25600'; // 25MB
                $messages['pdf_file.required'] = 'Please upload a PDF file';
                $messages['pdf_file.mimes'] = 'File must be in PDF format';
                $messages['pdf_file.max'] = 'PDF size cannot exceed 25MB';
                break;

            case 'link':
                $rules['external_url'] = 'required|url|max:500';
                $messages['external_url.required'] = 'External URL is required';
                $messages['external_url.url'] = 'Please enter a valid URL';
                break;

            case 'meeting':
                $rules['meeting_url'] = 'required|url|max:500';
                $messages['meeting_url.required'] = 'Meeting link is required';
                $messages['meeting_url.url'] = 'Please enter a valid meeting link';
                break;
        }

        // Validate request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'booking_id' => $bookingId,
                'trainer_id' => $trainerId,
                'module_id' => $request->module_id,
                'mode' => $mode,
                'content_type' => $request->content_type,
                'title' => $request->title,
                'description' => $request->description,
                'is_visible_to_org' => $request->has('is_visible_to_org') ? 1 : 0,
                'is_visible_to_candidates' => $request->has('is_visible_to_candidates') ? 1 : 0,
            ];

            // Handle content type specific data
            switch ($request->content_type) {
                case 'video':
                    if ($request->hasFile('video_file')) {
                        $file = $request->file('video_file');
                        $path = $file->store('training_content/videos/' . $bookingId, 'public');
                        $data['file_path'] = $path;
                    }
                    break;

                case 'text':
                    $data['text_content'] = $request->text_content;
                    break;

                case 'pdf':
                    if ($request->hasFile('pdf_file')) {
                        $file = $request->file('pdf_file');
                        $path = $file->store('training_content/pdfs/' . $bookingId, 'public');
                        $data['file_path'] = $path;
                    }
                    break;

                case 'link':
                    $data['external_url'] = $request->external_url;
                    break;

                case 'meeting':
                    $data['external_url'] = $request->input('meeting_url');
                    break;
            }

            $content = TrainingContent::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Content added successfully!',
                'data' => $content
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save content: ' . $e->getMessage()
            ], 500);
        }
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