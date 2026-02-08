<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCohortStoreRequest;
use App\Http\Requests\AdminCohortUpdateRequest;
use App\Models\Cohort;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CohortController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $cohorts = Cohort::query()
            ->with(['rules', 'calendarTemplate:id,name'])
            ->withCount([
                'members as active_members_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'members as waitlist_members_count' => function ($query) {
                    $query->where('status', 'waitlist');
                },
            ])
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $cohorts]);
    }

    public function store(AdminCohortStoreRequest $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validated();

        $cohort = DB::transaction(function () use ($validated, $user) {
            $cohort = Cohort::query()->create([
                'name' => $validated['name'],
                'course_type' => $validated['course_type'] ?? null,
                'enrollment_type' => $validated['enrollment_type'] ?? null,
                'calendar_template_id' => $validated['calendar_template_id'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'auto_assign' => $validated['auto_assign'] ?? true,
                'is_active' => $validated['is_active'] ?? true,
                'created_by_employee_id' => $user->id,
            ]);

            foreach ($validated['rules'] ?? [] as $rule) {
                $cohort->rules()->create([
                    'field' => $rule['field'],
                    'operator' => $rule['operator'],
                    'value' => $rule['value'],
                    'is_active' => $rule['is_active'] ?? true,
                ]);
            }

            return $cohort;
        });

        return response()->json([
            'data' => $cohort->load(['rules', 'calendarTemplate:id,name']),
        ], 201);
    }

    public function update(AdminCohortUpdateRequest $request, int $cohortId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $cohort = Cohort::query()->find($cohortId);
        if (!$cohort) {
            return response()->json(['message' => 'Cohort not found.'], 404);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($cohort, $validated) {
            $cohort->fill([
                'name' => $validated['name'] ?? $cohort->name,
                'course_type' => array_key_exists('course_type', $validated) ? $validated['course_type'] : $cohort->course_type,
                'enrollment_type' => array_key_exists('enrollment_type', $validated) ? $validated['enrollment_type'] : $cohort->enrollment_type,
                'calendar_template_id' => array_key_exists('calendar_template_id', $validated) ? $validated['calendar_template_id'] : $cohort->calendar_template_id,
                'capacity' => array_key_exists('capacity', $validated) ? $validated['capacity'] : $cohort->capacity,
                'starts_at' => array_key_exists('starts_at', $validated) ? $validated['starts_at'] : $cohort->starts_at,
                'ends_at' => array_key_exists('ends_at', $validated) ? $validated['ends_at'] : $cohort->ends_at,
                'auto_assign' => $validated['auto_assign'] ?? $cohort->auto_assign,
                'is_active' => $validated['is_active'] ?? $cohort->is_active,
            ]);
            $cohort->save();

            if (array_key_exists('rules', $validated)) {
                $cohort->rules()->delete();
                foreach ($validated['rules'] as $rule) {
                    $cohort->rules()->create([
                        'field' => $rule['field'],
                        'operator' => $rule['operator'],
                        'value' => $rule['value'],
                        'is_active' => $rule['is_active'] ?? true,
                    ]);
                }
            }
        });

        return response()->json([
            'data' => $cohort->fresh()->load(['rules', 'calendarTemplate:id,name']),
        ]);
    }

    public function members(Request $request, int $cohortId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $cohort = Cohort::query()->find($cohortId);
        if (!$cohort) {
            return response()->json(['message' => 'Cohort not found.'], 404);
        }

        $members = $cohort->members()
            ->with('student:id,first_name,surnames,email,course_type,enrollment_type,status')
            ->orderByRaw("case when status = 'active' then 0 else 1 end")
            ->orderByDesc('assigned_at')
            ->get();

        return response()->json([
            'data' => [
                'cohort' => $cohort,
                'members' => $members,
            ],
        ]);
    }
}
