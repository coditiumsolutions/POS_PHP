<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Logout is a safe, idempotent action. Exempting it avoids a 419
        // "Page Expired" when the CSRF token is stale (e.g. expired session).
        'logout',
    ];
}
