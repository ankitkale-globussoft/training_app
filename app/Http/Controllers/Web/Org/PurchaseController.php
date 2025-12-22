<?php

namespace App\Http\Controllers\Web\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orgId = Auth::guard('org_web')->user()->org_id;

            $query = Booking::with(['requirement.program', 'trainer'])
                ->where('org_id', $orgId);

            // Search by Program Title or Transaction ID
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('requirement.program', function ($subQ) use ($search) {
                            $subQ->where('title', 'like', "%{$search}%");
                        });
                });
            }

            // Filter by Payment Status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('payment_status', $request->status);
            }

            // Sort
            if ($request->has('sort')) {
                switch ($request->sort) {
                    case 'date_desc':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'date_asc':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'amount_desc':
                        $query->orderBy('amount', 'desc');
                        break;
                    case 'amount_asc':
                        $query->orderBy('amount', 'asc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $purchases = $query->paginate(10);

            return response()->json([
                'status' => true,
                'html' => view('organisation.purchases.partials.purchase-list', compact('purchases'))->render(),
                'pagination' => $purchases->links('pagination::bootstrap-5')->toHtml()
            ]);
        }

        return view('organisation.purchases.index');
    }

    public function invoice($booking_id)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        $booking = Booking::with(['requirement.program', 'trainer', 'organization'])
            ->where('booking_id', $booking_id)
            ->where('org_id', $orgId)
            ->firstOrFail();

        return view('organisation.purchases.invoice', compact('booking'));
    }
}
