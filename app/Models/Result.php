<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'user_id',
        'conclusion',
        'examination_result_pdf',
        'approved_psikolog_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
