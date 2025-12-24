<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json([
            'profile' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $allowedFields = ['full_name', 'bio', 'title', 'city', 'category', 'user_role', 'provider_type'];
        $user->update($request->only($allowedFields));

        return response()->json([
            'message' => 'Profile updated',
            'profile' => $user
        ]);
    }

    public function switchRole(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $user->user_role = $request->input('user_role', $user->user_role);
        $user->save();

        return response()->json([
            'message' => 'Role switched',
            'profile' => $user
        ]);
    }

    public function uploadAvatar(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!$request->hasFile('avatar')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $file = $request->file('avatar');

        $validated = $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $file->store('avatars', 'public'); 

        // $user->avatar_url = '/storage/' . $path;
        $user->avatar_url = asset('storage/' . $path);
        $user->save();

        return response()->json([
            'message' => 'Avatar uploaded successfully',
            'avatar_url' => $user->avatar_url,
        ]);
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'user_role' => 'required|string|in:client,provider',
            'provider_type' => 'nullable|string|in:freelance,local',
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->full_name = $request->full_name;
        $user->user_role = $request->user_role;
        $user->provider_type = $request->user_role === 'provider' ? $request->provider_type : null;
        $user->updated_at = now();
        $user->save();

        return response()->json([
            'message' => 'Profile upserted successfully',
            'profile' => $user,
        ]);
    }
}
