<?php

namespace App\Enums;

enum PositionLevel: string
{
    case ENTRY = 'entry';
    case MID = 'mid';
    case SENIOR = 'senior';
    case LEAD = 'lead';
    case MANAGER = 'manager';
    case DIRECTOR = 'director';
    case VP = 'vp';
    case C_LEVEL = 'c_level';
} 