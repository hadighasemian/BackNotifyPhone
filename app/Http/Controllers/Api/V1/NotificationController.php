<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Device;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        /** @var Device $device */
        $device = $request->user();
        $payload = $request->all();
        $items = array_is_list($payload) ? $payload : (isset($payload[0]) || isset($payload['device_notif_key'])
            ? (isset($payload['device_notif_key']) ? [$payload] : $payload)
            : (isset($payload['notifications']) ? $payload['notifications'] : [$payload]));

        if (! is_array($items) || count($items) === 0) {
            return response()->json(['message' => 'No notifications provided.', 'errors' => (object) []], 422);
        }

        $accepted = [];
        $duplicates = [];
        $errors = [];

        foreach ($items as $i => $item) {
            $v = Validator::make($item, [
                'device_notif_key' => 'required|string|max:191',
                'package_name' => 'required|string|max:191',
                'app_label' => 'nullable|string|max:191',
                'title' => 'nullable|string',
                'text' => 'nullable|string',
                'subtext' => 'nullable|string',
                'posted_at' => 'nullable|date',
                'extras' => 'nullable|array',
                'status' => 'nullable|in:received,processed',
            ]);
            if ($v->fails()) {
                $errors[$i] = $v->errors()->toArray();
                continue;
            }
            $data = $v->validated();
            $existing = Notification::where('device_id', $device->id)->where('device_notif_key', $data['device_notif_key'])->first();
            if ($existing) {
                $duplicates[] = $existing->id;
                continue;
            }
            $n = Notification::create([
                'device_id' => $device->id,
                'device_notif_key' => $data['device_notif_key'],
                'package_name' => $data['package_name'],
                'app_label' => $data['app_label'] ?? null,
                'title' => $data['title'] ?? null,
                'text' => $data['text'] ?? null,
                'subtext' => $data['subtext'] ?? null,
                'posted_at' => $data['posted_at'] ?? null,
                'received_at' => now(),
                'status' => $data['status'] ?? 'received',
                'extras' => $data['extras'] ?? null,
            ]);
            $accepted[] = $n->id;
        }

        $device->forceFill(['last_seen_at' => now()])->save();

        return response()->json([
            'data' => [
                'accepted' => $accepted,
                'duplicates' => $duplicates,
                'errors' => $errors,
                'status' => empty($errors) ? 'ok' : 'partial',
            ],
        ], empty($accepted) && ! empty($errors) ? 422 : 200);
    }
}
