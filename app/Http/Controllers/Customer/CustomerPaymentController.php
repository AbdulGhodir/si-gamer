<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    public function checkout(Payment $payment)
    {
        abort_unless(
            $payment->booking()->where('id_user', Auth::id())->exists(),
            403
        );

        if ($payment->status_pembayaran !== 'menunggu' || !$payment->snap_token) {
            return redirect()
                ->route('customer.riwayat')
                ->with('error', 'Pembayaran ini tidak bisa dilanjutkan.');
        }

        $payment->load('booking.device');

        return view('customer.checkout', compact('payment'));
    }

    public function handle(Request $request)
    {
        $data = $request->all();

        $orderId = (string) ($data['order_id'] ?? '');
        $statusCode = (string) ($data['status_code'] ?? '');
        $grossAmount = (string) ($data['gross_amount'] ?? '');
        $receivedSignature = (string) ($data['signature_key'] ?? '');

        $signatureKey = config('midtrans.signature_key');

        $expectedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $signatureKey
        );

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('kode_transaksi', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ((int) round((float) $grossAmount) !== (int) $payment->jumlah) {
            return response()->json(['message' => 'Amount mismatch'], 403);
        }

        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? null;

        DB::transaction(function () use ($payment, $transactionStatus, $fraudStatus, $data) {
            $payment->refresh();

            if ($payment->status_pembayaran === 'berhasil') {
                return;
            }

            if (
                in_array($transactionStatus, ['settlement', 'capture'], true) &&
                ($fraudStatus === null || $fraudStatus === 'accept')
            ) {
                $payment->update([
                    'status_pembayaran' => 'berhasil',
                    'waktu_pembayaran' => $data['settlement_time'] ?? now(),
                ]);

                return;
            }

            if ($transactionStatus === 'pending') {
                $payment->update([
                    'status_pembayaran' => 'menunggu',
                ]);

                return;
            }

            if ($transactionStatus === 'expire') {
                $payment->update([
                    'status_pembayaran' => 'kedaluwarsa',
                ]);

                if ($payment->booking->status === 'menunggu') {
                    $payment->booking->update(['status' => 'dibatalkan']);
                }

                return;
            }

            if ($transactionStatus === 'cancel') {
                $payment->update([
                    'status_pembayaran' => 'dibatalkan',
                ]);

                if ($payment->booking->status === 'menunggu') {
                    $payment->booking->update(['status' => 'dibatalkan']);
                }
            }

            if ($transactionStatus ==='deny') {
                $payment->update([
                    'status_pembayaran' => 'gagal',
                ]);

                if ($payment->booking->status === 'menunggu') {
                    $payment->booking->update(['status' => 'dibatalkan']);
                }
            }
        });

        return response()->json(['message' => 'Notification processed']);
    }
}
