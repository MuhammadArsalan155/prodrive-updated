<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login/validate',
        'json-test'
    ];
     /**
     * Determine if the request has a valid CSRF token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // For certain routes, skip strict CSRF checking
        $skipRoutes = [
            'login/validate',
            'json-test'
        ];

        if (in_array($request->path(), $skipRoutes)) {
            return true;
        }

        // Log CSRF token details for debugging
        Log::info('CSRF Verification', [
            'path' => $request->path(),
            'method' => $request->method(),
            'csrf_token' => $request->session()->token(),
            'input_token' => $request->input('_token'),
            'header_token' => $request->header('X-CSRF-TOKEN')
        ]);

        return parent::tokensMatch($request);
    }
}
