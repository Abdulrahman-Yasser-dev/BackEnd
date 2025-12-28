<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\WorkRequest;
use App\Models\Notification;

class ChatController extends Controller
{
    public function getMessages($workRequestId)
    {
        $messages = Message::where('work_request_id', $workRequestId)
            ->with(['sender', 'receiver'])
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

        // Create Notification (wrap in try-catch to prevent 500 if non-critical notification fails)
        try {
            Notification::create([
                'user_id' => $validated['receiver_id'],
                'type' => 'new_message',
                'title' => 'New Message',
                'message' => 'You have a new message from ' . $user->full_name,
                'data' => [
                    'work_request_id' => $validated['work_request_id'],
                    'sender_id' => $user->id
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("Notification failed: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load(['sender', 'receiver'])
        ], 201);
    }

    public function getConversations()
    {
        $userId = auth()->id();

        $workRequests = WorkRequest::where('user_id', $userId)
            ->orWhereHas('messages', function ($query) use ($userId) {
                $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->with(['user', 'messages' => function ($query) {
                $query->latest()->with(['sender', 'receiver']);
            }])
            ->get()
            ->map(function ($wr) {
                $wr->last_message = $wr->messages->first();
                unset($wr->messages);
                return $wr;
            });

        return response()->json([
            'conversations' => $workRequests
        ]);
    }
}
