<?php

namespace App\Enums;

enum ProviderEnum: string
{
    case GOOGLE = 'google';
    case FACEBOOK = 'facebook';
    case GITHUB = 'github';
}
