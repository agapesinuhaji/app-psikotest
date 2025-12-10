<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'reporting_pdf',
        'status',
        'is_result_processed',
    ];

    public function invoices()
    {
        // return $this->hasMany(Invoice::class);
    }

    // Relasi yang benar
    public function users()
    {
        return $this->hasMany(User::class, 'batch_id');
    }
}
