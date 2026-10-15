<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppVersionPublicController extends Controller
{
    public function latest()
    {
        $v = AppVersion::latestActive();
        if (! $v) {
            return response()->json(['message' => 'No active app version.', 'errors' => (object) []], 404);
        }
        return response()->json(['data' => [
            'version_name' => $v->version_name,
            'version_code' => $v->version_code,
            'release_notes' => $v->release_notes,
            'download_url' => url('/api/v1/app/download'),
            'file_size' => $v->file_size,
            'sha256' => $v->sha256,
        ]]);
    }

    public function download(?int $code = null): StreamedResponse|\Illuminate\Http\JsonResponse
    {
        $v = $code
            ? AppVersion::where('version_code', $code)->first()
            : AppVersion::latestActive();
        if (! $v || ! Storage::disk('local')->exists($v->file_path)) {
            return response()->json(['message' => 'APK not found.', 'errors' => (object) []], 404);
        }
        $v->increment('downloads');
        $filename = 'phonenotify-'.$v->version_name.'-'.$v->version_code.'.apk';
        return Storage::disk('local')->download($v->file_path, $filename, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}
