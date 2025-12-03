<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientTest extends Model
{
    use SoftDeletes;

    protected $table = 'client_tests';

    protected $fillable = [
        'user_id',
        'spm_start_at',
        'spm_end_at',
        'papikostick_start_at',
        'papikostick_end_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
