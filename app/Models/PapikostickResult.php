<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PapikostickResult extends Model
{
    protected $table = 'papikostick_results';

    protected $fillable = [
        'user_id',
        'result_orientation',
        'flexibility',
        'systematic_work',
        'g_c_n_score',
        'g_c_n_conclusion',
        'achievement_motivation',
        'cooperation',
        'interpersonal_skills',
        'emotional_stability',
        'a_w_s_k_score',
        'a_w_s_k_conclusion',
        'self_development',
        'managing_change',
        'z_c_score',
        'z_c_conclusion',
        'start_time',
        'end_time',
        'is_finish',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
