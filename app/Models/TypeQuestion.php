<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeQuestion extends Model
{
    protected $fillable = ['name', 'photo', 'duration', 'description', 'status'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'type_question_id');
    }
}
