<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;

class EnsureDevice
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() instanceof Device) {
            return response()->json(['message' => 'Device token required.', 'errors' => (object) []], 403);
        }
        return $next($request);
    }
}
