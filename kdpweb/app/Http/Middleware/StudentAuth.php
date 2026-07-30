<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('student_id')) {
            return redirect()->route('login')->withErrors([
                'enrollment_number' => 'Please login as a student to continue.',
            ]);
        }

        return $next($request);
    }
}
