<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'student';

    public $timestamps = false;

    protected $hidden = ['password'];
    
    public function subjectControls(): HasMany
    {
        return $this->hasMany(ControlSubjectStudent::class, 'student_id');
    }
}
