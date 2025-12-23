<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkRequest;

class WorkRequestController extends Controller
{
    public function index()
    {
        $workRequests = WorkRequest::all();
        return response()->json([
            'workRequests' => $workRequests
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'category' => 'required|string',
            'work_title' => 'required|string|max:255',
            'work_description' => 'required|string',
            'service_type' => 'required|string',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'expected_date' => 'nullable|date',
            'file_attachments' => 'nullable|array',
        ]);

        $workRequest = WorkRequest::create($request->all());

        return response()->json([
            'message' => 'Work request submitted successfully',
            'work_request' => $workRequest
        ], 201);
    }
}
