<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case EARLY_LEAVE = 'early_leave';
    case HALF_DAY = 'half_day';
} 