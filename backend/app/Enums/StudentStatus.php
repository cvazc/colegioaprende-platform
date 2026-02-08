<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case OnboardingPending = 'onboarding_pending';
    case InProgress = 'in_progress';
    case FinalEvaluationReady = 'final_evaluation_ready';
    case Certified = 'certified';
    case Inactive = 'inactive';
}
