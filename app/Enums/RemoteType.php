<?php

namespace App\Enums;

enum RemoteType: string
{
    case REMOTE = 'remote';
    case HYBRID = 'hybrid';
    case ON_SITE = 'on-site';
} 