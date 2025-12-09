<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPMResult extends Model
{
    protected $table = 'spm_results';

    protected $fillable = [
        'user_id',
        'logical_thinking',
        'analytical_power',
        'numerical_ability',
        'verbal_ability',
        'grade',
        'category',
        'score',
        'start_time',
        'end_time',
        'is_finish',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
