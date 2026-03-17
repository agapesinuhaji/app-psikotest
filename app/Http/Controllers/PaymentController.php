<?php

namespace App\Http\Controllers;

use App\Models\CorporateIdentity;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function show($orderId)
    {
        $payment = Payment::where('order_id', $orderId)->firstOrFail();

        $hargaPerPeserta = CorporateIdentity::value('price') ?? 0;

        return view('payment', [
            'payment' => $payment,
            'hargaPerPeserta' => $hargaPerPeserta,
        ]);
    }
}