<?php

namespace App\Enums;

enum SubjectStatus: int
{
    case Disabled = 0;
    case Enabled = 1;
    case Approved = 2;
}