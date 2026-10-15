<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppVersionRequest;
use App\Http\Requests\UpdateAppVersionRequest;
use App\Http\Resources\AppVersionResource;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppVersionAdminController extends Controller
{
    public function index(Request $request)
    {
        $p = AppVersion::query()->orderByDesc('version_code')
            ->paginate(min((int) $request->query('per_page', 20), 100));
        return AppVersionResource::collection($p)->additional(['meta' => [
            'current_page' => $p->currentPage(), 'last_page' => $p->lastPage(),
            'per_page' => $p->perPage(), 'total' => $p->total(),
        ]]);
    }

    public function store(StoreAppVersionRequest $request)
    {
        $file = $request->file('apk');
        $code = (int) $request->version_code;
        $path = $file->storeAs('apks', 'v'.$code.'.apk', 'local');
        $hash = hash_file('sha256', Storage::disk('local')->path($path));
        $active = $request->boolean('is_active', false);

        $version = DB::transaction(function () use ($request, $path, $hash, $file, $code, $active) {
            if ($active) {
                AppVersion::query()->update(['is_active' => false]);
            }
            return AppVersion::create([
                'version_name' => $request->version_name,
                'version_code' => $code,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'sha256' => $hash,
                'release_notes' => $request->release_notes,
                'is_active' => $active,
                'downloads' => 0,
            ]);
        });

        return response()->json(['data' => new AppVersionResource($version)], 201);
    }

    public function show(AppVersion $app_version)
    {
        return response()->json(['data' => new AppVersionResource($app_version)]);
    }

    public function update(UpdateAppVersionRequest $request, AppVersion $app_version)
    {
        $data = $request->validated();
        if (array_key_exists('is_active', $data) && $data['is_active']) {
            AppVersion::query()->where('id', '!=', $app_version->id)->update(['is_active' => false]);
        }
        $app_version->fill($data)->save();
        return response()->json(['data' => new AppVersionResource($app_version)]);
    }

    public function activate(AppVersion $app_version)
    {
        AppVersion::query()->where('id', '!=', $app_version->id)->update(['is_active' => false]);
        $app_version->update(['is_active' => true]);
        return response()->json(['data' => new AppVersionResource($app_version)]);
    }

    public function destroy(AppVersion $app_version)
    {
        if ($app_version->file_path) {
            Storage::disk('local')->delete($app_version->file_path);
        }
        $app_version->delete();
        return response()->json(['data' => ['message' => 'Deleted']]);
    }
}
