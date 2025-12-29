<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Use the email specifically configured in .env
            $recipient = env('MAIL_FROM_ADDRESS');

            Mail::to($recipient)->send(new ContactMail(
                $request->name,
                $request->email,
                $request->message
            ));

            return response()->json(['message' => 'Message sent successfully'], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Contact Email Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }
}
