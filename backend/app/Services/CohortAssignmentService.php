<?php

namespace App\Services;

use App\Models\Cohort;
use App\Models\CohortAssignmentLog;
use App\Models\CohortMember;
use App\Models\Employee;
use App\Models\Student;
use App\Models\StudentCalendar;

class CohortAssignmentService
{
    public function assignAutomatically(Student $student, ?string $reason = null): ?CohortMember
    {
        $existing = CohortMember::query()->where('student_id', $student->id)->first();
        if ($existing && $existing->status === 'active') {
            return $existing;
        }

        $cohorts = Cohort::query()
            ->where('is_active', true)
            ->where('auto_assign', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString());
            })
            ->with(['rules' => function ($query) {
                $query->where('is_active', true);
            }])
            ->withCount(['members as active_members_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderByRaw('COALESCE(starts_at, CURRENT_DATE) asc')
            ->orderBy('id')
            ->get();

        $matching = $cohorts->filter(function (Cohort $cohort) use ($student) {
            return $this->matchesStudent($cohort, $student);
        })->values();

        if ($matching->isEmpty()) {
            return null;
        }

        $withCapacity = $matching->first(function (Cohort $cohort) {
            return $this->hasCapacity($cohort);
        });

        if ($withCapacity) {
            return $this->assignStudent($student, $withCapacity, null, 'auto', 'auto_match', false);
        }

        return $this->assignStudent($student, $matching->first(), null, 'auto', 'waitlist_auto', false, 'waitlist');
    }

    public function assignManually(
        Student $student,
        Cohort $cohort,
        ?Employee $actor,
        ?string $reason = null,
        bool $force = false
    ): CohortMember {
        return $this->assignStudent($student, $cohort, $actor, 'manual', $reason, $force);
    }

    private function assignStudent(
        Student $student,
        Cohort $cohort,
        ?Employee $actor,
        string $source,
        ?string $reason,
        bool $force,
        ?string $overrideStatus = null
    ): CohortMember {
        $existing = CohortMember::query()->where('student_id', $student->id)->first();
        $fromCohortId = $existing?->cohort_id;

        $status = $overrideStatus ?? ($force || $this->hasCapacity($cohort) ? 'active' : 'waitlist');

        $member = CohortMember::query()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'cohort_id' => $cohort->id,
                'assigned_by_employee_id' => $actor?->id,
                'assigned_at' => now(),
                'status' => $status,
                'source' => $source,
                'notes' => $reason,
            ]
        );

        CohortAssignmentLog::query()->create([
            'student_id' => $student->id,
            'from_cohort_id' => $fromCohortId,
            'to_cohort_id' => $cohort->id,
            'action' => $status === 'active' ? 'assigned' : 'waitlisted',
            'reason' => $reason,
            'actor_employee_id' => $actor?->id,
            'meta' => [
                'source' => $source,
                'force' => $force,
            ],
        ]);

        if ($status === 'active' && $cohort->calendar_template_id) {
            StudentCalendar::query()->updateOrCreate(
                ['student_id' => $student->id],
                [
                    'calendar_template_id' => $cohort->calendar_template_id,
                    'assigned_by_employee_id' => $actor?->id,
                    'assigned_start_date' => now()->toDateString(),
                ]
            );
        }

        return $member;
    }

    private function hasCapacity(Cohort $cohort): bool
    {
        if ($cohort->capacity === null || $cohort->capacity <= 0) {
            return true;
        }

        $activeCount = isset($cohort->active_members_count)
            ? (int) $cohort->active_members_count
            : CohortMember::query()
                ->where('cohort_id', $cohort->id)
                ->where('status', 'active')
                ->count();

        return $activeCount < $cohort->capacity;
    }

    private function matchesStudent(Cohort $cohort, Student $student): bool
    {
        if ($cohort->course_type && $cohort->course_type !== $student->course_type) {
            return false;
        }

        if ($cohort->enrollment_type && $cohort->enrollment_type !== $student->enrollment_type) {
            return false;
        }

        $rules = $cohort->relationLoaded('rules')
            ? $cohort->rules
            : $cohort->rules()->where('is_active', true)->get();

        if ($rules->isEmpty()) {
            return true;
        }

        return $rules->every(function ($rule) use ($student) {
            $actualValue = data_get($student, $rule->field);

            return $this->evaluateRule((string) $rule->operator, $actualValue, (string) $rule->value);
        });
    }

    private function evaluateRule(string $operator, mixed $actualValue, string $expectedValue): bool
    {
        if ($actualValue instanceof \BackedEnum) {
            $actualValue = $actualValue->value;
        } elseif ($actualValue instanceof \UnitEnum) {
            $actualValue = $actualValue->name;
        }

        $actual = (string) $actualValue;

        return match ($operator) {
            'equals' => $actual === $expectedValue,
            'not_equals' => $actual !== $expectedValue,
            'in' => in_array($actual, $this->splitCsv($expectedValue), true),
            'not_in' => !in_array($actual, $this->splitCsv($expectedValue), true),
            default => false,
        };
    }

    private function splitCsv(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
