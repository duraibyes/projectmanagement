<?php

use App\Helpers\AccessGuard;

if (!function_exists('access')) {
    function access()
    {
        return new AccessGuard();
    }
}