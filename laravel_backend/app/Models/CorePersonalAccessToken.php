<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose PersonalAccessToken pointing to core schema — shared ecosystem table
 */

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

class CorePersonalAccessToken extends PersonalAccessToken
{
    protected $table = 'core.personal_access_tokens';
}
