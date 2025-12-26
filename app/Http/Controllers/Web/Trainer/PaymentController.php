<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function view()
    {
        $trainerId = \Illuminate\Support\Facades\Auth::guard('trainer_web')->user()->trainer_id;

        // Completed Bookings (Earnings)
        $earnings = \App\Models\Booking::with(['requirement.program', 'organization'])
            ->where('trainer_id', $trainerId)
            ->whereHas('progress', function ($q) {
                $q->where('status', 'completed');
            })
            ->latest()
            ->paginate(10, ['*'], 'earnings_page');

        // Payouts (Requests)
        $payouts = \App\Models\PaymentRequest::where('trainer_id', $trainerId)
            ->latest()
            ->paginate(10, ['*'], 'payouts_page');

        return view('trainer.payments.payment', compact('earnings', 'payouts'));
    }

    public function requestPayment()
    {
        $trainerId = \Illuminate\Support\Facades\Auth::guard('trainer_web')->user()->trainer_id;

        // Total Earnings: Sum of completed bookings where trainer is assigned
        // Assuming 'completed' status in booking means payment is done by org.
        // And 'completed' in progress means trainer finished work.
        // For simplicity, we use booking's completed payment_status + booking_status 'assigned' or 'completed'.
        // Better: Check BookingProgress where status is 'completed' or Booking where payment_status 'completed'.

        // Let's use Booking payment_status = 'completed' AND booked amount. 
        // NOTE: Ideally we should check if trainer actually finished training.
        // But per prompt "based on Bookings he completed @[app/Models/BookingProgress.php]".
        // So we filter Bookings where associated progress has status 'completed'.

        $completedBookings = \App\Models\Booking::where('trainer_id', $trainerId)
            ->whereHas('progress', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('amount');

        // Total Withdrawn (Approved requests)
        $withdrawn = \App\Models\PaymentRequest::where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->sum('amount');

        // Pending Requests
        $pending = \App\Models\PaymentRequest::where('trainer_id', $trainerId)
            ->where('status', 'pending')
            ->sum('amount');

        $available = $completedBookings - $withdrawn - $pending;

        $requests = \App\Models\PaymentRequest::where('trainer_id', $trainerId)
            ->latest()
            ->paginate(10);

        return view('trainer.payments.request', compact('completedBookings', 'withdrawn', 'pending', 'available', 'requests'));
    }
    public function storeRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $trainerId = \Illuminate\Support\Facades\Auth::guard('trainer_web')->user()->trainer_id;

        // Recalculate available balance to prevent race conditions (basic check)
        $completedBookings = \App\Models\Booking::where('trainer_id', $trainerId)
            ->whereHas('progress', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('amount');

        $withdrawn = \App\Models\PaymentRequest::where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->sum('amount');

        $pending = \App\Models\PaymentRequest::where('trainer_id', $trainerId)
            ->where('status', 'pending')
            ->sum('amount');

        $available = $completedBookings - $withdrawn - $pending;

        if ($request->amount > $available) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        \App\Models\PaymentRequest::create([
            'trainer_id' => $trainerId,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Payment request submitted successfully.');
    }

    public function viewAccountDetails()
    {
        $trainerId = \Illuminate\Support\Facades\Auth::guard('trainer_web')->user()->trainer_id;
        $bankDetails = \App\Models\TrainerBankDetail::where('trainer_id', $trainerId)->first();
        return view('trainer.payments.account_details', compact('bankDetails'));
    }

    public function storeAccountDetails(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'upi_id' => 'nullable|string|max:50'
        ]);

        $trainerId = \Illuminate\Support\Facades\Auth::guard('trainer_web')->user()->trainer_id;

        \App\Models\TrainerBankDetail::updateOrCreate(
            ['trainer_id' => $trainerId],
            [
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id
            ]
        );

        return back()->with('success', 'Account details updated successfully.');
    }
}
