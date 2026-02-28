<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Batch;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Ambil semua payload dari Midtrans
        $payload = $request->all();

        // Simpan log untuk debugging
        Log::info('MIDTRANS CALLBACK:', $payload);

        $orderId        = $payload['order_id'] ?? null;
        $statusCode     = $payload['status_code'] ?? null;
        $grossAmount    = $payload['gross_amount'] ?? null;
        $signatureKey   = $payload['signature_key'] ?? null;

        // ======================================
        // 🔐 VALIDASI SIGNATURE KEY
        // ======================================
        $serverKey = config('midtrans.server_key');

        $generatedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $serverKey
        );

        if ($generatedSignature !== $signatureKey) {
            Log::warning('❌ Invalid signature from Midtrans', [
                'expected' => $generatedSignature,
                'received' => $signatureKey,
            ]);

            return response()->json([
                'message' => 'Invalid signature'
            ], 403);
        }

        // ======================================
        // 🔎 Ambil data Payment
        // ======================================
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            Log::warning('❌ Payment not found for order_id: ' . $orderId);

            return response()->json([
                'message' => 'Payment not found'
            ], 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;

        // ======================================
        // 🔄 Mapping status dari Midtrans
        // ======================================
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $payment->status = 'pending';
            } elseif ($fraudStatus == 'accept') {
                $payment->status = 'paid';
            }
        } 
        elseif ($transactionStatus == 'settlement') {
            $payment->status = 'paid';
        } 
        elseif ($transactionStatus == 'pending') {
            $payment->status = 'pending';
        } 
        elseif ($transactionStatus == 'deny') {
            $payment->status = 'failed';
        } 
        elseif ($transactionStatus == 'expire') {
            $payment->status = 'expired';
        } 
        elseif ($transactionStatus == 'cancel') {
            $payment->status = 'cancelled';
        }

        // Simpan perubahan payment
        $payment->save();

        Log::info('✅ Payment updated', [
            'order_id' => $orderId,
            'status'   => $payment->status
        ]);

        // ======================================
        // 🎯 Jika status PAID → update Batch
        // ======================================
        if ($payment->status === 'paid') {
            $batch = Batch::find($payment->batch_id);

            if ($batch) {
                $batch->status = 'paid'; // atau "active"
                $batch->save();

                Log::info('✅ Batch updated to PAID', [
                    'batch_id' => $batch->id
                ]);
            }
        }

        return response()->json([
            'message' => 'Callback handled successfully'
        ]);
    }
}