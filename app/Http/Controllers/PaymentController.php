<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingWorkflow;
use App\Services\SslCommerz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentController extends Controller
{
    /** Customer clicks "Pay Online" on their dashboard. */
    public function pay(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        if (! SslCommerz::enabled()) {
            Alert::info(__('Online payment unavailable'), __('Please pay in cash to your gardener on the service day.'));

            return back();
        }

        if ($booking->isPaid() || in_array($booking->status, ['cancelled', 'rejected'])) {
            Alert::info(__('Nothing to pay'), __('This booking does not need a payment.'));

            return back();
        }

        $amount = $booking->amountDue();
        if ($amount <= 0) {
            Alert::info(__('Price not set yet'), __('We will confirm the price with you before payment.'));

            return back();
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'tran_id' => 'GRD'.$booking->id.'-'.Str::upper(Str::random(10)),
            'amount' => $amount,
            'currency' => config('gardener.currency', 'BDT'),
            'status' => 'pending',
        ]);

        try {
            $result = SslCommerz::initiate([
                'total_amount' => number_format($amount, 2, '.', ''),
                'tran_id' => $payment->tran_id,
                'success_url' => route('payment.success'),
                'fail_url' => route('payment.fail'),
                'cancel_url' => route('payment.cancel'),
                'ipn_url' => route('payment.ipn'),
                'cus_name' => $booking->name,
                'cus_email' => $booking->email,
                'cus_phone' => $booking->phone ?: (Auth::user()->phone ?? '01700000000'),
                'cus_add1' => $booking->address ?: 'N/A',
                'cus_city' => 'N/A',
                'product_name' => $booking->service?->title ?? 'Gardening service',
            ]);
        } catch (\Throwable $e) {
            Log::error('SSLCommerz init failed: '.$e->getMessage());
            $result = [];
        }

        if (($result['status'] ?? '') !== 'SUCCESS' || empty($result['GatewayPageURL'])) {
            $payment->update(['status' => 'failed', 'gateway_response' => $result]);
            Alert::error(__('Payment error'), __('Could not start the payment. Please try again later.'));

            return back();
        }

        return redirect()->away($result['GatewayPageURL']);
    }

    /*
    | SSLCommerz sends the customer's browser back here with a POST.
    | (These routes are excluded from CSRF in bootstrap/app.php.)
    */

    public function success(Request $request)
    {
        $payment = $this->confirm($request->input('tran_id'), $request->input('val_id'));

        return redirect()->route('user.dashboard', ['payment' => $payment?->status === 'paid' ? 'success' : 'failed']);
    }

    public function fail(Request $request)
    {
        $this->closePayment($request->input('tran_id'), 'failed', $request->all());

        return redirect()->route('user.dashboard', ['payment' => 'failed']);
    }

    public function cancel(Request $request)
    {
        $this->closePayment($request->input('tran_id'), 'cancelled', $request->all());

        return redirect()->route('user.dashboard', ['payment' => 'cancelled']);
    }

    /** Server-to-server notification from SSLCommerz (works even if the customer closed the tab). */
    public function ipn(Request $request)
    {
        $payment = $this->confirm($request->input('tran_id'), $request->input('val_id'));

        return response()->json(['ok' => $payment?->status === 'paid']);
    }

    /**
     * Validate a payment with SSLCommerz and mark the booking paid.
     * Safe to call more than once for the same transaction.
     */
    private function confirm(?string $tranId, ?string $valId): ?Payment
    {
        if (! $tranId || ! $valId) {
            return null;
        }

        $payment = Payment::where('tran_id', $tranId)->first();
        if (! $payment) {
            return null;
        }

        if ($payment->status === 'paid') {
            return $payment;
        }

        try {
            $check = SslCommerz::validate($valId);
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validation failed: '.$e->getMessage());

            return $payment;
        }

        $valid = in_array($check['status'] ?? '', ['VALID', 'VALIDATED'])
            && ($check['tran_id'] ?? null) === $payment->tran_id
            && abs((float) ($check['amount'] ?? 0) - (float) $payment->amount) < 0.01
            && strtoupper($check['currency_type'] ?? $payment->currency) === strtoupper($payment->currency);

        if (! $valid) {
            $payment->update(['status' => 'failed', 'gateway_response' => $check]);
            ActivityLog::record('payment.invalid', "Payment {$payment->tran_id} failed validation", $payment->booking);

            return $payment;
        }

        DB::transaction(function () use ($payment, $check, $valId) {
            $method = $check['card_issuer'] ?? $check['card_type'] ?? 'Online';
            $payment->update(['status' => 'paid', 'val_id' => $valId, 'method' => $method, 'gateway_response' => $check]);
            BookingWorkflow::markPaid($payment->booking, 'Online ('.$method.')', (float) $payment->amount);
        });

        return $payment->fresh();
    }

    private function closePayment(?string $tranId, string $status, array $response): void
    {
        Payment::where('tran_id', $tranId)->where('status', 'pending')
            ->update(['status' => $status, 'gateway_response' => json_encode($response)]);
    }
}
