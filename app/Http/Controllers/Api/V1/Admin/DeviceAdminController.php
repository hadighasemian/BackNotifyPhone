<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\DeviceStateResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Device::query()->with('latestState')->withCount(['notifications', 'appErrors', 'states'])->orderByDesc('last_seen_at');
        if ($request->query('status') === 'online') $q->online();
        if ($request->query('status') === 'offline') $q->offline();
        if ($b = $request->query('brand')) $q->where('brand', $b);
        if ($s = $request->query('search')) {
            $q->where(fn ($w) => $w->where('label', 'like', "%$s%")->orWhere('model', 'like', "%$s%")
                ->orWhere('device_uuid', 'like', "%$s%")->orWhere('manufacturer', 'like', "%$s%"));
        }
        $p = $q->paginate(min((int) $request->query('per_page', 20), 100));
        return DeviceResource::collection($p)->additional(['meta' => [
            'current_page' => $p->currentPage(), 'last_page' => $p->lastPage(),
            'per_page' => $p->perPage(), 'total' => $p->total(),
        ]]);
    }

    public function show(Device $device)
    {
        $device->load('latestState')->loadCount(['notifications', 'appErrors', 'states']);
        return response()->json(['data' => new DeviceResource($device)]);
    }

    public function states(Request $request, Device $device)
    {
        $q = $device->states()->orderByDesc('reported_at');
        if ($request->boolean('with_location')) {
            $q->whereNotNull('latitude')->whereNotNull('longitude');
        }
        $p = $q->paginate(min((int) $request->query('per_page', 50), 200));
        return DeviceStateResource::collection($p)->additional(['meta' => [
            'current_page' => $p->currentPage(), 'last_page' => $p->lastPage(),
            'per_page' => $p->perPage(), 'total' => $p->total(),
        ]]);
    }

    public function map()
    {
        $devices = Device::query()
            ->with(['latestLocatedState'])
            ->whereHas('states', fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'))
            ->get()
            ->map(function (Device $d) {
                $s = $d->latestLocatedState;
                if (! $s) return null;
                return [
                    'id' => $d->id,
                    'device_uuid' => $d->device_uuid,
                    'label' => $d->label,
                    'brand' => $d->brand,
                    'model' => $d->model,
                    'is_online' => $d->is_online,
                    'latitude' => $s->latitude,
                    'longitude' => $s->longitude,
                    'location_accuracy' => $s->location_accuracy,
                    'reported_at' => $s->reported_at?->toIso8601ZuluString(),
                    'last_seen_at' => $d->last_seen_at?->toIso8601ZuluString(),
                ];
            })->filter()->values();
        return response()->json(['data' => $devices]);
    }

    public function update(Request $request, Device $device)
    {
        $data = $request->validate(['label' => 'nullable|string|max:191']);
        $device->fill($data)->save();
        return response()->json(['data' => new DeviceResource($device->load('latestState'))]);
    }

    public function destroy(Device $device)
    {
        $device->tokens()->delete();
        $device->delete();
        return response()->json(['data' => ['message' => 'Deleted']]);
    }
}
