<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'type_question_id',
        'question',
        'is_active',
        'question_code',
    ];


    public function typeQuestion()
    {
        return $this->belongsTo(TypeQuestion::class, 'type_question_id');
    }

    public function options()
    {
        return $this->hasMany(Option::class, 'question_id');
    }
}
