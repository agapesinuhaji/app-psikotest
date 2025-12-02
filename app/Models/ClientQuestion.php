<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'question_id',
        'option_id',
        'score',
        'is_active',
    ];

    /**
     * Relasi ke user yang mengerjakan pertanyaan
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke soal
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * Relasi ke opsi jawaban
     */
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }
}
