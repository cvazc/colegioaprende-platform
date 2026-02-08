<?php

namespace App\Enums;

enum EmployeeAbility: string
{
    case ManageEmployees = 'manage_employees';
    case ManageProspects = 'manage_prospects';
    case ManageStudents = 'manage_students';
    case ManageCohorts = 'manage_cohorts';
    case ManageSubjects = 'manage_subjects';
    case ManageCalendars = 'manage_calendars';
    case ManagePayments = 'manage_payments';
    case ManageGraduates = 'manage_graduates';
}
