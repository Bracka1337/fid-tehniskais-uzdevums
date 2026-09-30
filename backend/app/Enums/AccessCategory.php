<?php

namespace App\Enums;

enum AccessCategory: string
{
    case Public = 'public';
    case Internal = 'internal';
    case Restricted = 'restricted';
    case Confidential = 'confidential';
}
