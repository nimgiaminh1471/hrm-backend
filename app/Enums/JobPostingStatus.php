<?php

namespace App\Enums;

enum JobPostingStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CLOSED = 'closed';
} 