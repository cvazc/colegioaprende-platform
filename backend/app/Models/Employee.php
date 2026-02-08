<?php

namespace App\Models;

use App\Enums\EmployeeAbility;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens;

    public const TYPE_ADMIN = 1;
    public const TYPE_STAFF = 2;

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

    protected $casts = [
        'employee_type' => 'integer',
    ];

    public function isAdmin(): bool
    {
        return $this->employee_type === self::TYPE_ADMIN;
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'admin' : 'staff';
    }

    public function permissions(): array
    {
        if ($this->isAdmin()) {
            return [
                EmployeeAbility::ManageEmployees->value => true,
                EmployeeAbility::ManageProspects->value => true,
                EmployeeAbility::ManageStudents->value => true,
                EmployeeAbility::ManageCohorts->value => true,
                EmployeeAbility::ManageSubjects->value => true,
                EmployeeAbility::ManageCalendars->value => true,
                EmployeeAbility::ManagePayments->value => true,
                EmployeeAbility::ManageGraduates->value => true,
            ];
        }

        return [
            EmployeeAbility::ManageEmployees->value => false,
            EmployeeAbility::ManageProspects->value => true,
            EmployeeAbility::ManageStudents->value => true,
            EmployeeAbility::ManageCohorts->value => true,
            EmployeeAbility::ManageSubjects->value => true,
            EmployeeAbility::ManageCalendars->value => true,
            EmployeeAbility::ManagePayments->value => true,
            EmployeeAbility::ManageGraduates->value => true,
        ];
    }

    public function canAccess(string $ability): bool
    {
        $permissions = $this->permissions();

        return (bool) ($permissions[$ability] ?? false);
    }
}
