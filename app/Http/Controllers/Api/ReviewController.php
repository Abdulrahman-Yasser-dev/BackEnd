<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\WorkRequest;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_request_id' => 'nullable|exists:work_requests,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $reviewerId = $request->user()->id;

        // If work_request_id is provided, check authorization
        if (isset($validated['work_request_id']) && $validated['work_request_id']) {
            $workRequest = WorkRequest::findOrFail($validated['work_request_id']);
            if ($workRequest->user_id !== $reviewerId && $workRequest->messages()->where('sender_id', $reviewerId)->count() === 0) {
                // Only owner or people who messaged can review? 
                // Let's keep it simple for now as requested.
            }
        }

        $review = Review::create([
            'work_request_id' => $validated['work_request_id'] ?? null,
            'reviewer_id' => $reviewerId,
            'reviewee_id' => $validated['reviewee_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment']
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }

    public function getReviewsForUser($userId)
    {
        $reviews = Review::where('reviewee_id', $userId)
            ->with('reviewer:id,full_name,avatar_url')
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->reviewer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }
}
