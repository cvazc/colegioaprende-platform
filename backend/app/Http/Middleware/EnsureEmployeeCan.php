<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeCan
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if (!$user->canAccess($ability)) {
            return response()->json([
                'message' => 'Forbidden for this employee role.',
                'required_ability' => $ability,
            ], 403);
        }

        return $next($request);
    }
}
