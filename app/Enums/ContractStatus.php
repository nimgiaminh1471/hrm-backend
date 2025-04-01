<?php

namespace App\Enums;

enum ContractStatus: string
{
    case ACTIVE = 'active';
    case TERMINATED = 'terminated';
    case EXPIRED = 'expired';
} 