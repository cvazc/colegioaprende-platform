<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'employee';

    public $timestamps = false;

    protected $hidden = ['password'];

    protected $fillable = [
        'first_name',
        'surnames',
        'email',
        'password',
        'employee_type',
    ];
}
