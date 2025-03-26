<?php

namespace App\Http\Controllers;

use App\Models\message as Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class MessagingController extends Controller
{
     /**
     * Send a new message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048', // Allowing common file types
        ]);

        $message = new Message();
        $message->sender_id = auth()->user()->id;
        $message->recipient_id = $request->recipient;
        $message->subject = $request->subject;
        $message->message = $request->message;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Unique filename
            $file->move(public_path('attachments'), $fileName); // Move to public/attachments
            $message->attachment = $fileName; // Store only the filename
        }

        $message->save();

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => $message,
            'attachment_url' => $message->attachment ? asset("attachments/{$message->attachment}") : null
        ], 201);
    }


    /**
     * Get inbox messages.
     */
    public function inbox()
    {
        $messages = Message::where('recipient', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return response()->json($messages);
    }

    /**
     * Get sent messages.
     */
    public function sent()
    {
        $messages = Message::where('sender_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return response()->json($messages);
    }

    public function getGroupedMessages(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();
    
        // Fetch all messages where the user is either the sender or recipient
        $allMessages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
        })->get();
    
        // Group messages by recipient (we group by the user who is the other party in the message)
        $groupedMessages = $allMessages->groupBy(function ($message) use ($user) {
            return $message->recipient_id == $user->id ? $message->sender_id : $message->recipient_id;
        });
    
        // Get all user details that are part of the conversation
        $userIds = $allMessages->pluck('sender_id')->merge($allMessages->pluck('recipient_id'))->unique();
    
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
    
        // Map the grouped messages to include user details (name)
        $groupedMessagesWithDetails = $groupedMessages->map(function ($messages, $userId) use ($users) {
            $recipient = $users->get($userId);
            return [
                'recipient' => $recipient ? $recipient->name : 'Unknown',
                'messages' => $messages
            ];
        });
    
        return response()->json($groupedMessagesWithDetails);
    }
    
}
