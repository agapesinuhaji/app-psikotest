<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class TypeQuestion extends Model
{
    protected $fillable = ['name', 'photo', 'duration', 'description', 'status', 'slug'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'type_question_id');
    }

    protected static function booted()
    {
        static::creating(function ($record) {
            if (empty($record->slug)) {
                $record->slug = Str::slug($record->name);
            }
        });

        static::updating(function ($record) {
            if (empty($record->slug)) {
                $record->slug = Str::slug($record->name);
            }
        });
    }
}
