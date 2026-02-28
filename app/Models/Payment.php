<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'batch_id',
        'order_id',
        'amount',
        'participants',
        'ppn',
        'unique_code',
        'snap_token',
        'payment_type',
        'status',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
