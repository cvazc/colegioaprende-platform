<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $table = 'question_bank';

    protected $fillable = [
        'subject_id',
        'type',
        'prompt',
        'topic',
        'explanation',
        'difficulty',
        'is_active',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'is_active' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }
}
