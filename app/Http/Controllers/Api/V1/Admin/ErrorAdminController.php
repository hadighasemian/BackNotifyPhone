<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppErrorResource;
use App\Models\AppError;
use Illuminate\Http\Request;

class ErrorAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = AppError::query()->with('device')->orderByDesc('created_at');
        if ($d = $request->query('device_id')) $q->where('device_id', $d);
        if ($l = $request->query('level')) $q->where('level', $l);
        if ($s = $request->query('search')) $q->where('message', 'like', "%$s%");
        $p = $q->paginate(min((int) $request->query('per_page', 20), 100));
        return AppErrorResource::collection($p)->additional(['meta' => [
            'current_page' => $p->currentPage(), 'last_page' => $p->lastPage(),
            'per_page' => $p->perPage(), 'total' => $p->total(),
        ]]);
    }

    public function show(AppError $error)
    {
        return response()->json(['data' => new AppErrorResource($error->load('device'))]);
    }
}
