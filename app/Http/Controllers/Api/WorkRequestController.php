<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkRequest;
use App\Models\Notification;
use App\Models\Message;

class WorkRequestController extends Controller
{
    public function index()
    {
        $workRequests = WorkRequest::with(['user', 'logs.changedBy'])->get();
        return response()->json([
            'workRequests' => $workRequests
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Count existing projects
        $projectCount = WorkRequest::where('user_id', $request->user()->id)->count();
        if ($projectCount >= 4) {
            return response()->json(['message' => 'Limit reached. You can only create up to 4 projects.'], 400);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'work_title' => 'required|string|max:255',
            'work_description' => 'required|string',
            'service_type' => 'required|string',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'expected_date' => 'nullable|date',
            'duration' => 'nullable|string',
            'budget_min' => 'nullable|numeric',
            'budget_max' => 'nullable|numeric',
            'category_ids' => 'nullable|array',
            'skill_ids' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        $data = $request->except('attachments');

        // Handle file uploads
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Store file and get path
                $path = $file->store('work_attachments', 'public');
                $attachmentPaths[] = asset('storage/' . $path);
            }
        }

        $data['file_attachments'] = $attachmentPaths;

        $workRequest = WorkRequest::create($data);

        return response()->json([
            'message' => 'Work request submitted successfully',
            'work_request' => $workRequest
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,pending_payment,delayed,completed',
            'notes' => 'nullable|string',
            'confirm' => 'nullable|boolean',
        ]);

        $workRequest = WorkRequest::findOrFail($id);
        $userId = $request->user()->id;
        $isOwner = $workRequest->user_id === $userId;

        $isProvider = Message::where('work_request_id', $id)
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->exists();

        if (!$isOwner) {
            $canConfirm = $validated['confirm'] && $workRequest->pending_status && $workRequest->pending_status_changed_by === $workRequest->user_id;

            if (!$canConfirm) {
                if (!$workRequest->provider_id || $workRequest->provider_id !== $userId) {
                    return response()->json(['message' => 'Unauthorized. Only the assigned provider can update status.'], 403);
                }
            } else {
                if (!$isProvider && (!$workRequest->provider_id || $workRequest->provider_id !== $userId)) {
                    return response()->json(['message' => 'Unauthorized. You must have at least messaged the client to confirm status.'], 403);
                }
            }
        }

        if ($validated['confirm'] && $workRequest->pending_status) {
            if ($workRequest->pending_status_changed_by === $userId) {
                return response()->json(['message' => 'You cannot confirm your own change request'], 400);
            }

            $oldStatus = $workRequest->status;
            $workRequest->status = $workRequest->pending_status;

            if ($workRequest->status === 'in_progress' && !$workRequest->provider_id) {
                $workRequest->provider_id = ($isOwner ? $workRequest->pending_status_changed_by : $userId);
            }

            $workRequest->pending_status = null;
            $workRequest->pending_status_changed_by = null;
            $workRequest->save();

            $workRequest->logs()->create([
                'old_status' => $oldStatus,
                'new_status' => $workRequest->status,
                'changed_by_id' => $userId,
                'notes' => ($validated['notes'] ?? '') . " (Confirmed change)",
            ]);

            $recipientId = ($isOwner ? ($workRequest->provider_id ?: $workRequest->pending_status_changed_by) : $workRequest->user_id);
            if ($recipientId) {
                Notification::create([
                    'user_id' => $recipientId,
                    'type' => 'status_confirmed',
                    'title' => 'Status Change Confirmed',
                    'message' => "The status change to '{$workRequest->status}' has been confirmed for '{$workRequest->work_title}'.",
                    'data' => [
                        'work_request_id' => $id,
                        'new_status' => $workRequest->status
                    ]
                ]);
            }

            // Mark any pending_status notifications for THIS user and THIS work request as read
            Notification::where('user_id', $userId)
                ->where('type', 'status_pending')
                ->whereJsonContains('data->work_request_id', (int)$id)
                ->update(['read' => true]);

            return response()->json([
                'message' => 'Status change confirmed',
                'work_request' => $workRequest->load('logs')
            ]);
        }

        // Check if we are rejecting a pending change
        if ($request->input('reject') && $workRequest->pending_status) {
            if ($workRequest->pending_status_changed_by === $userId) {
                return response()->json(['message' => 'You cannot reject your own change request'], 400);
            }

            $rejectedStatus = $workRequest->pending_status;
            $workRequest->pending_status = null;
            $workRequest->pending_status_changed_by = null;
            $workRequest->save();

            // Create log entry for rejection
            $workRequest->logs()->create([
                'old_status' => $workRequest->status,
                'new_status' => $workRequest->status, // No change
                'changed_by_id' => $userId,
                'notes' => ($validated['notes'] ?? '') . " (Rejected change to {$rejectedStatus})",
            ]);

            // Notify the other party about rejection
            $recipientId = ($isOwner ? ($workRequest->provider_id ?: $workRequest->pending_status_changed_by) : $workRequest->user_id);
            if ($recipientId) {
                \App\Models\Notification::create([
                    'user_id' => $recipientId,
                    'type' => 'status_rejected',
                    'title' => 'Status Change Rejected',
                    'message' => "The status change to '{$rejectedStatus}' has been rejected for '{$workRequest->work_title}'.",
                    'data' => [
                        'work_request_id' => $id,
                        'rejected_status' => $rejectedStatus
                    ]
                ]);
            }

            // Mark any pending_status notifications for THIS user and THIS work request as read
            Notification::where('user_id', $userId)
                ->where('type', 'status_pending')
                ->whereJsonContains('data->work_request_id', (int)$id)
                ->update(['read' => true]);

            return response()->json([
                'message' => 'Status change rejected',
                'work_request' => $workRequest->load('logs')
            ]);
        }

        // If not confirming, we are proposing/directly updating
        // For 'new' status, maybe anyone can revert? No, let's just use pending for everything.

        $workRequest->pending_status = $validated['status'];
        $workRequest->pending_status_changed_by = $userId;
        $workRequest->save();

        // Notify the other party
        $recipientId = ($isOwner ? ($workRequest->provider_id ?: 0) : $workRequest->user_id);

        // If owner proposes but no provider fixed yet, notify all who messaged? 
        // For now, let's keep it simple: notify the other participant.
        if (!$recipientId && $isOwner) {
            // Pick the last provider who messaged
            $lastMsg = Message::where('work_request_id', $id)
                ->where('sender_id', '!=', $userId)
                ->latest()
                ->first();
            $recipientId = $lastMsg ? $lastMsg->sender_id : null;
        }

        if ($recipientId && $recipientId != $userId) {
            Notification::create([
                'user_id' => $recipientId,
                'type' => 'status_pending',
                'title' => 'Status Change Requested',
                'message' => "A status change to '{$validated['status']}' has been requested for '{$workRequest->work_title}'. Please confirm.",
                'data' => [
                    'work_request_id' => $id,
                    'pending_status' => $validated['status']
                ]
            ]);
        }

        return response()->json([
            'message' => 'Status change requested. Waiting for other party to confirm.',
            'work_request' => $workRequest->load('logs')
        ]);
    }

    public function assignProvider(Request $request, $id)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:users,id',
        ]);

        $workRequest = WorkRequest::findOrFail($id);

        if ($workRequest->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized. Only the owner can assign a provider.'], 403);
        }

        if ($workRequest->provider_id) {
            return response()->json(['message' => 'A provider has already been assigned to this request.'], 400);
        }

        $workRequest->provider_id = $validated['provider_id'];
        $workRequest->status = 'in_progress'; // Automatically move to in_progress when assigned? 
        $workRequest->save();

        // Create log entry
        $workRequest->logs()->create([
            'old_status' => 'new',
            'new_status' => 'in_progress',
            'changed_by_id' => $request->user()->id,
            'notes' => "Provider assigned by client.",
        ]);

        // Notify the assigned provider
        \App\Models\Notification::create([
            'user_id' => $validated['provider_id'],
            'type' => 'status_update',
            'title' => 'Project Assigned',
            'message' => "You have been assigned to '{$workRequest->work_title}'.",
            'data' => [
                'work_request_id' => $id,
                'status' => 'in_progress'
            ]
        ]);

        return response()->json([
            'message' => 'Provider assigned successfully',
            'work_request' => $workRequest->load('logs')
        ]);
    }

    public function myRequests(Request $request)
    {
        $user = $request->user();
        $requests = WorkRequest::where('user_id', $user->id)
            ->with(['logs' => function ($query) {
                $query->latest();
            }])
            ->latest()
            ->get();

        return response()->json($requests);
    }
}
