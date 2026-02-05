<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    protected $table = 'pricing';

    protected $fillable = [
        'course_type',
        'enrollment_type',
        'amount',
        'currency',
        'is_active',
    ];
}
