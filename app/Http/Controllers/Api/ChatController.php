<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\WorkRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function getMessages($workRequestId)
    {
        $userId = Auth::id();
        $otherUserId = request()->query('other_user_id');

        $query = Message::where('work_request_id', $workRequestId)
            ->where(function ($q) use ($userId) {
                // Must be my message
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            });

        if ($otherUserId) {
            // And must involve the specific other party
            $query->where(function ($q) use ($otherUserId) {
                $q->where('sender_id', $otherUserId)
                    ->orWhere('receiver_id', $otherUserId);
            });
        }

        $messages = $query->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'work_request_id' => 'required|exists:work_requests,id',
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $user = $request->user();

        $message = Message::create([
            'work_request_id' => $validated['work_request_id'],
            'sender_id' => $user->id,
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        // Create Notification
        try {
            Notification::create([
                'user_id' => $validated['receiver_id'],
                'type' => 'new_message',
                // Store keys instead of hardcoded text to allow frontend translation
                'title' => 'notifications.new_message_title',
                'message' => 'notifications.new_message_body',
                'data' => [
                    'work_request_id' => $validated['work_request_id'],
                    'sender_id' => $user->id,
                    'sender_name' => $user->full_name // Pass name for parameter substitution
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Notification failed: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load(['sender', 'receiver'])
        ], 201);
    }

    public function getConversations()
    {
        $userId = Auth::id();

        // 1. Fetch all messages involving the user
        $messages = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        })
            ->with(['sender', 'receiver', 'workRequest'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Group by (work_request_id + other_user_id)
        $conversations = $messages->groupBy(function ($message) use ($userId) {
            $otherUserId = ($message->sender_id == $userId)
                ? $message->receiver_id
                : $message->sender_id;
            return $message->work_request_id . '-' . $otherUserId;
        });

        // 3. Format response
        $formatted = $conversations->map(function ($msgs) use ($userId) {
            $lastMsg = $msgs->first();
            $otherUser = ($lastMsg->sender_id == $userId) ? $lastMsg->receiver : $lastMsg->sender;
            $workRequest = $lastMsg->workRequest;

            if (!$workRequest) return null; // Handle deleted projects logic if needed

            return [
                'id' => $lastMsg->work_request_id . '-' . $otherUser->id, // Composite unique ID for list key
                'work_request_id' => $lastMsg->work_request_id,
                'user_id' => $otherUser->id, // The 'counterpart' ID
                'work_title' => $workRequest->work_title,
                'status' => $workRequest->status,
                'service_type' => $workRequest->service_type,
                'city' => $workRequest->city,
                'expected_date' => $workRequest->expected_date,
                'budget_min' => $workRequest->budget_min,
                'budget_max' => $workRequest->budget_max,
                'duration' => $workRequest->duration,
                'created_at' => $workRequest->created_at,
                'last_message' => $lastMsg,
                'other_user' => $otherUser,
                // Legacy fields if needed by Messages.jsx (though we are refactoring it)
                'user' => $otherUser // Treat 'user' as other_user for backward compat if I miss a spot
            ];
        })->filter()->values();

        return response()->json([
            'conversations' => $formatted
        ]);
    }
}
