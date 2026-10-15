<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()->orderByDesc('id');
        if ($s = $request->query('search')) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        $p = $q->paginate(min((int) $request->query('per_page', 20), 100));
        return UserResource::collection($p)->additional(['meta' => [
            'current_page' => $p->currentPage(), 'last_page' => $p->lastPage(),
            'per_page' => $p->perPage(), 'total' => $p->total(),
        ]]);
    }

    public function store(StoreUserRequest $request)
    {
        $u = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->input('role', 'admin'),
        ]);
        return response()->json(['data' => new UserResource($u)], 201);
    }

    public function show(User $user)
    {
        return response()->json(['data' => new UserResource($user)]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->fill($data)->save();
        return response()->json(['data' => new UserResource($user)]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself.', 'errors' => (object) []], 422);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['data' => ['message' => 'Deleted']]);
    }
}
