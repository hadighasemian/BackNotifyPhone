<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Notification::query()->with('device')->orderByDesc('received_at');
        if ($d = $request->query('device_id')) $q->where('device_id', $d);
        if ($p = $request->query('package')) $q->where('package_name', 'like', "%$p%");
        if ($p = $request->query('package_name')) $q->where('package_name', $p);
        if ($s = $request->query('status')) $q->where('status', $s);
        if ($from = $request->query('from') ?? $request->query('date_from')) $q->where('received_at', '>=', $from);
        if ($to = $request->query('to') ?? $request->query('date_to')) $q->where('received_at', '<=', $to);
        if ($search = $request->query('search')) {
            $q->where(fn ($w) => $w->where('title', 'like', "%$search%")->orWhere('text', 'like', "%$search%")
                ->orWhere('package_name', 'like', "%$search%"));
        }
        $p = $q->paginate(min((int) $request->query('per_page', 20), 100));
        return NotificationResource::collection($p)->additional(['meta' => [
            'current_page' => $p->currentPage(), 'last_page' => $p->lastPage(),
            'per_page' => $p->perPage(), 'total' => $p->total(),
        ]]);
    }

    public function show(Notification $notification)
    {
        return response()->json(['data' => new NotificationResource($notification->load('device'))]);
    }
}
