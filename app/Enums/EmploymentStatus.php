<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case TERMINATED = 'terminated';
    case RESIGNED = 'resigned';
    case SUSPENDED = 'suspended';
} 