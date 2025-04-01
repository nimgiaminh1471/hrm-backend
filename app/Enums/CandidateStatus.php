<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case APPLIED = 'applied';
    case SCREENING = 'screening';
    case INTERVIEWED = 'interviewed';
    case OFFERED = 'offered';
    case HIRED = 'hired';
    case REJECTED = 'rejected';
} 