<?php

namespace App\Models\Enums;

enum LeadStatusType: string
{
    case New = 'NEW';
    case Pending = 'PENDING';
    case Done = 'DONE';
}
