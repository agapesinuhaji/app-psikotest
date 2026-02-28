<?php 

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(array $data)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => $data['gross_amount'],
            ],
            'customer_details' => $data['customer_details'],
        ];

        return Snap::getSnapToken($params);
    }
}