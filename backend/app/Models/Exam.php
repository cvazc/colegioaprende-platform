<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exam';

    public $timestamps = false;

    protected $fillable = [
        'subject_id',
        'exam_name',
    ];
}
