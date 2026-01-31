<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Display the conversation with the coach.
     */
    public function index(): View
    {
        $client = auth()->user();
        $coach = $client->coach;

        if (! $coach) {
            abort(404, 'No coach assigned');
        }

        // Mark unread messages from coach as read
        Message::where('sender_id', $coach->id)
            ->where('receiver_id', $client->id)
            ->unread()
            ->update(['read_at' => now()]);

        // Get all messages between client and coach
        $messages = Message::betweenUsers($client->id, $coach->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('client.messages', [
            'coach' => $coach,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to the coach.
     */
    public function store(Request $request): RedirectResponse
    {
        $client = auth()->user();
        $coach = $client->coach;

        if (! $coach) {
            abort(404, 'No coach assigned');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        Message::create([
            'sender_id' => $client->id,
            'receiver_id' => $coach->id,
            'body' => $validated['body'],
        ]);

        return redirect()->route('client.messages');
    }

    /**
     * Get new messages for polling (AJAX).
     */
    public function poll(Request $request): JsonResponse
    {
        $client = auth()->user();
        $coach = $client->coach;

        if (! $coach) {
            abort(404, 'No coach assigned');
        }

        $lastMessageId = $request->get('last_id', 0);

        $newMessages = Message::betweenUsers($client->id, $coach->id)
            ->where('id', '>', $lastMessageId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages from coach as read
        Message::where('sender_id', $coach->id)
            ->where('receiver_id', $client->id)
            ->where('id', '>', $lastMessageId)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $newMessages->map(fn ($msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'is_mine' => $msg->sender_id === $client->id,
                'created_at' => $msg->created_at->format('M d, g:i A'),
            ]),
        ]);
    }
}
