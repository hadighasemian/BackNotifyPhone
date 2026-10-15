<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        if (! $u instanceof User || ! $u->isAdmin()) {
            return response()->json(['message' => 'Admin access required.', 'errors' => (object) []], 403);
        }
        return $next($request);
    }
}
