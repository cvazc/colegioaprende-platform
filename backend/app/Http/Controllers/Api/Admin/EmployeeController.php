<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminEmployeeStoreRequest;
use App\Http\Requests\AdminEmployeeUpdateRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $employees = Employee::query()
            ->orderByDesc('id')
            ->get()
            ->map(function (Employee $employee) {
                return [
                    'id' => $employee->id,
                    'first_name' => $employee->first_name,
                    'surnames' => $employee->surnames,
                    'email' => $employee->email,
                    'employee_type' => $employee->employee_type,
                    'role_label' => $employee->roleLabel(),
                    'permissions' => $employee->permissions(),
                ];
            });

        return response()->json([
            'data' => $employees,
        ]);
    }

    public function store(AdminEmployeeStoreRequest $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validated();

        $employee = Employee::query()->create([
            'first_name' => $validated['first_name'],
            'surnames' => $validated['surnames'],
            'email' => $validated['email'],
            'employee_type' => $validated['employee_type'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'data' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'surnames' => $employee->surnames,
                'email' => $employee->email,
                'employee_type' => $employee->employee_type,
                'role_label' => $employee->roleLabel(),
                'permissions' => $employee->permissions(),
            ],
        ], 201);
    }

    public function update(AdminEmployeeUpdateRequest $request, int $employeeId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $employee = Employee::query()->find($employeeId);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found.'], 404);
        }

        $validated = $request->validated();

        if (array_key_exists('first_name', $validated)) {
            $employee->first_name = $validated['first_name'];
        }
        if (array_key_exists('surnames', $validated)) {
            $employee->surnames = $validated['surnames'];
        }
        if (array_key_exists('email', $validated)) {
            $employee->email = $validated['email'];
        }
        if (array_key_exists('employee_type', $validated)) {
            $employee->employee_type = $validated['employee_type'];
        }
        if (array_key_exists('password', $validated) && !empty($validated['password'])) {
            $employee->password = Hash::make($validated['password']);
        }

        $employee->save();

        return response()->json([
            'data' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'surnames' => $employee->surnames,
                'email' => $employee->email,
                'employee_type' => $employee->employee_type,
                'role_label' => $employee->roleLabel(),
                'permissions' => $employee->permissions(),
            ],
        ]);
    }
}
