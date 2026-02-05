<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use App\Models\StudentCreditWallet;

class Student extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'student';

    public $timestamps = false;

    protected $hidden = ['password'];

    public function subjectControls(): HasMany
    {
        return $this->hasMany(ControlSubjectStudent::class, 'student_id');
    }

    public function creditWallet(): HasOne
    {
        return $this->hasOne(StudentCreditWallet::class, 'student_id');
    }

    protected $fillable = [
        'prospect_id',
        'first_name',
        'surnames',
        'local_phone',
        'movil_phone',
        'birth_date',
        'actual_address',
        'city',
        'actual_state',
        'occupation',
        'curp',
        'emergency_contact',
        'deposit_date',
        'email',
        'password',
        'course_type',
        'enrollment_type',
    ];
}
