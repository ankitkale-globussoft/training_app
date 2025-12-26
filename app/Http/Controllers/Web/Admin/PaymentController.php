<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function view()
    {
        return view('admin.payment.view');
    }

    public function getPaymentsData(Request $request)
    {
        try {
            $query = Booking::with([
                'organization:org_id,name,email,mobile,org_image',
                'trainer:trainer_id,name,email,phone,profile_pic',
                'requirement.program:program_id,title'
            ]);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('organization', function ($orgQuery) use ($search) {
                        $orgQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhereHas('trainer', function ($trainerQuery) use ($search) {
                            $trainerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhere('transaction_id', 'like', "%{$search}%")
                        ->orWhere('booking_id', 'like', "%{$search}%");
                });
            }

            // Payment status filter
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Booking status filter
            if ($request->filled('booking_status')) {
                $query->where('booking_status', $request->booking_status);
            }

            // Amount range filter
            if ($request->filled('min_amount')) {
                $query->where('amount', '>=', $request->min_amount);
            }

            if ($request->filled('max_amount')) {
                $query->where('amount', '<=', $request->max_amount);
            }

            // Date range filter
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            // Handle special sort cases
            if ($sortBy === 'org_name') {
                $query->leftJoin('organizations', 'bookings.org_id', '=', 'organizations.org_id')
                    ->select('bookings.*')
                    ->orderBy('organizations.name', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Get paginated results
            $perPage = $request->get('per_page', 10);
            $payments = $query->paginate($perPage);

            // Calculate stats
            $stats = [
                'total_records' => $payments->total(),
                'total_amount' => Booking::when($request->filled('payment_status'), function ($q) use ($request) {
                    return $q->where('payment_status', $request->payment_status);
                })
                    ->when($request->filled('search'), function ($q) use ($request) {
                        $search = $request->search;
                        return $q->where(function ($subQ) use ($search) {
                            $subQ->whereHas('organization', function ($orgQuery) use ($search) {
                                $orgQuery->where('name', 'like', "%{$search}%");
                            })
                                ->orWhere('transaction_id', 'like', "%{$search}%");
                        });
                    })
                    ->sum('amount')
            ];

            return response()->json([
                'status' => true,
                'data' => $payments,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch payments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showPaymentDetails($id)
    {
        try {
            $payment = Booking::with([
                'organization:org_id,name,email,mobile,org_image,addr_line1,city,state',
                'trainer:trainer_id,name,email,phone,profile_pic',
                'requirement' => function ($query) {
                    $query->with('program:program_id,title,duration,cost,min_students');
                }
            ])->findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch payment details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewTrainerPayments()
    {
        $pendingRequests = \App\Models\PaymentRequest::with('trainer')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        $history = \App\Models\PaymentRequest::with('trainer')
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        return view('admin.payment.trainer_payments', compact('pendingRequests', 'history'));
    }

    public function getTrainerBankDetails($id)
    {
        $details = \App\Models\TrainerBankDetail::where('trainer_id', $id)->first();
        if (!$details) {
            return response()->json(['status' => false, 'message' => 'Bank details not found']);
        }
        return response()->json(['status' => true, 'data' => $details]);
    }

    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:payment_requests,request_id',
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $paymentRequest = \App\Models\PaymentRequest::findOrFail($request->request_id);

        $paymentRequest->status = $request->status;
        $paymentRequest->admin_note = $request->admin_note;

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $paymentRequest->payment_proof = $path;
        }

        $paymentRequest->save();

        return back()->with('success', 'Payment status updated successfully.');
    }
}