<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $orgId = Auth::user()->org_id;

            $query = Booking::with(['requirement.program', 'trainer'])
                ->where('org_id', $orgId);

            // Search by Program Title or Transaction ID
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('requirement.program', function ($subQ) use ($search) {
                            $subQ->where('title', 'like', "%{$search}%");
                        });
                });
            }

            // Filter by Payment Status
            if ($request->filled('status')) {
                $query->where('payment_status', $request->status);
            }

            // Sort
            $sort = $request->get('sort', 'date_desc');
            switch ($sort) {
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'date_desc':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $purchases = $query->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Purchases fetched successfully',
                'data' => $purchases,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch purchases: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function invoice($booking_id)
    {
        try {
            $orgId = Auth::user()->org_id;

            $booking = Booking::with(['requirement.program', 'trainer', 'organization'])
                ->where('booking_id', $booking_id)
                ->where('org_id', $orgId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => 'Invoice details fetched successfully',
                'data' => $booking,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found or access denied',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }
}
