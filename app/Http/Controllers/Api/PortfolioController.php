<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class PortfolioController extends Controller
{
    public function index($userId)
    {
        $user = User::find($userId);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        return response()->json(['portfolio' => $user->portfolio ?? []]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'link' => 'nullable|string|max:255',
        ]);

        $user = User::find($request->user_id);
        $portfolio = $user->portfolio ?? [];

        $portfolio[] = [
            'id' => time(),
            'title' => $request->title,
            'description' => $request->description ?? '',
            'images' => $request->images ?? [],
            'link' => $request->link ?? '',
        ];

        $user->portfolio = $portfolio;
        $user->save();

        return response()->json(['message' => 'Portfolio item added', 'portfolio' => $portfolio]);
    }

    public function destroy(Request $request, $userId, $itemId)
    {
        $user = User::find($userId);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $portfolio = $user->portfolio ?? [];
        $newPortfolio = array_filter($portfolio, fn($item) => $item['id'] != $itemId);

        $user->portfolio = array_values($newPortfolio);
        $user->save();

        return response()->json(['message' => 'Portfolio item deleted', 'portfolio' => $user->portfolio]);
    }
}
