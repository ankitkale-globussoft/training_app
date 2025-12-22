<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
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

            if ($sortBy === 'org_name') {
                $query->leftJoin('organizations', 'bookings.org_id', '=', 'organizations.org_id')
                    ->select('bookings.*')
                    ->orderBy('organizations.name', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

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
                'success' => true,
                'message' => 'Payments data fetched successfully',
                'data' => [
                    'payments' => $payments,
                    'stats' => $stats
                ],
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payments: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
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
                'success' => true,
                'message' => 'Payment details fetched successfully',
                'data' => $payment,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment details not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }
}
