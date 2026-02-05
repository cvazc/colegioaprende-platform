<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    protected $table = 'prospect';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'surnames',
        'local_phone',
        'movil_phone',
        'birth_date',
        'actual_address',
        'city',
        'actual_state',
        'email',
        'occupation',
        'curp',
        'emergency_contact',
        'deposit_date',
        'course_type',
        'enrollment_type',
    ];
}
