<?php

namespace App\Enums;

enum CareerStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    case DRAFT = 'draft';
} 