<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function show($orderId)
    {
        $payment = Payment::where('order_id', $orderId)->firstOrFail();

        return view('payment', [
            'payment' => $payment
        ]);
    }
}