<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInformation;
use Illuminate\Http\Request;

class UserInformationController extends Controller
{
    public function index()
    {
        return UserInformation::all();
    }

    public function show($id)
    {
        $user = UserInformation::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:user_information,email',
            'phone'           => 'required|string|max:20',
            'country'         => 'required|string|max:100',
            'city'            => 'required|string|max:100',
            'provider_type'   => 'required|string|max:50',
            'services'        => 'required|array',
            'services.*'      => 'string',
            'description'     => 'nullable|string|min:10|max:1000',
            'portfolio_link'  => 'nullable|url|max:255',
        ]);

        UserInformation::create($validated);

        return response("Created Successfully", 201);
    }

    public function update(Request $request, $id)
    {
        $user = UserInformation::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:100',
            'email'          => 'sometimes|required|email|unique:user_information,email,' . $id,
            'phone'          => 'sometimes|required|string|max:20',
            'country'        => 'sometimes|required|string|max:100',
            'city'           => 'sometimes|required|string|max:100',
            'provider_type'  => 'sometimes|required|string|max:50',
            'services'        => 'required|array',
            'services.*'      => 'string',
            'description'    => 'nullable|string|min:0|max:1000',
            'portfolio_link' => 'nullable|url|max:255',
        ]);


        $user->update($validated);

        return response()->json([
            'message' => 'Updated Successfully',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = UserInformation::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Deleted Successfully']);
    }
}
