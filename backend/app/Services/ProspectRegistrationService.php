<?php

namespace App\Services;

use App\Enums\ProspectStatus;
use App\Enums\StudentStatus;
use App\Enums\SubjectStatus;
use App\Models\ControlSubjectStudent;
use App\Models\Exam;
use App\Models\Prospect;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProspectRegistrationService
{
    public function register(Prospect $prospect): Student
    {
        if (Student::query()->where('email', $prospect->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Prospect already registered as student.'],
            ]);
        }

        $requiredFields = [
            'first_name',
            'surnames',
            'actual_state',
            'email',
            'course_type',
            'enrollment_type',
        ];

        foreach ($requiredFields as $field) {
            if (empty($prospect->{$field})) {
                throw ValidationException::withMessages([
                    $field => ['Prospect is missing required data.'],
                ]);
            }
        }

        $subjects = Subject::query()
            ->where('enrollment_type', $prospect->enrollment_type)
            ->orderBy('id')
            ->get();

        if ($subjects->isEmpty()) {
            throw ValidationException::withMessages([
                'enrollment_type' => ['No subjects found for enrollment type.'],
            ]);
        }

        return DB::transaction(function () use ($prospect, $subjects) {
            $student = Student::query()->create([
                'prospect_id' => $prospect->id,
                'first_name' => $prospect->first_name,
                'surnames' => $prospect->surnames,
                'local_phone' => $prospect->local_phone,
                'movil_phone' => $prospect->movil_phone,
                'birth_date' => $prospect->birth_date,
                'actual_address' => $prospect->actual_address,
                'city' => $prospect->city,
                'actual_state' => $prospect->actual_state,
                'occupation' => $prospect->occupation,
                'curp' => $prospect->curp,
                'emergency_contact' => $prospect->emergency_contact,
                'deposit_date' => $prospect->deposit_date,
                'email' => $prospect->email,
                'password' => Hash::make(Str::random(12)),
                'course_type' => $prospect->course_type,
                'enrollment_type' => $prospect->enrollment_type,
                'status' => StudentStatus::OnboardingPending,
                'profile_completed_at' => null,
            ]);

            foreach ($subjects as $subject) {
                $examId = Exam::query()
                    ->where('subject_id', $subject->id)
                    ->orderBy('id')
                    ->value('id');

                if (!$examId) {
                    throw ValidationException::withMessages([
                        'exam_id' => ['Missing exam for subject ' . $subject->id . '.'],
                    ]);
                }

                ControlSubjectStudent::query()->create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'exam_id' => $examId,
                    'score' => 0,
                    'subject_status' => SubjectStatus::Disabled->value,
                ]);
            }

            $prospect->status = ProspectStatus::RegisteredStudent;
            $prospect->registered_as_student_at = now();
            $prospect->save();

            app(CohortAssignmentService::class)->assignAutomatically($student, 'prospect_registered');

            return $student;
        });
    }
}
