<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Display the message inbox with conversations.
     */
    public function index(): View
    {
        $coach = auth()->user();

        // Get all clients with their latest message and unread count
        $conversations = $coach->clients()
            ->get()
            ->map(function ($client) use ($coach) {
                $latestMessage = Message::betweenUsers($coach->id, $client->id)
                    ->latest()
                    ->first();

                $unreadCount = Message::where('sender_id', $client->id)
                    ->where('receiver_id', $coach->id)
                    ->unread()
                    ->count();

                return [
                    'client' => $client,
                    'latest_message' => $latestMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->filter(fn ($conv) => $conv['latest_message'] !== null)
            ->sortByDesc(fn ($conv) => $conv['latest_message']->created_at);

        // Get clients without messages for starting new conversations
        $clientsWithoutMessages = $coach->clients()
            ->get()
            ->filter(function ($client) use ($coach) {
                return ! Message::betweenUsers($coach->id, $client->id)->exists();
            });

        return view('coach.messages.index', compact('conversations', 'clientsWithoutMessages'));
    }

    /**
     * Display a conversation with a specific client.
     */
    public function show(User $user): View
    {
        $coach = auth()->user();

        // Ensure this is the coach's client
        if ($user->coach_id !== $coach->id) {
            abort(403);
        }

        // Mark unread messages from client as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $coach->id)
            ->unread()
            ->update(['read_at' => now()]);

        // Get messages between coach and client
        $messages = Message::betweenUsers($coach->id, $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('coach.messages.show', [
            'client' => $user,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to a client.
     */
    public function store(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $coach = auth()->user();

        if ($user->coach_id !== $coach->id) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::create([
            'sender_id' => $coach->id,
            'receiver_id' => $user->id,
            'body' => $validated['body'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'is_mine' => true,
                    'created_at' => $message->created_at->format('M d, g:i A'),
                ],
            ]);
        }

        return redirect()->route('coach.messages.show', $user);
    }

    /**
     * Get new messages for polling (AJAX).
     */
    public function poll(Request $request, User $user): JsonResponse
    {
        $coach = auth()->user();

        if ($user->coach_id !== $coach->id) {
            abort(403);
        }

        $lastMessageId = $request->get('last_id', 0);

        $newMessages = Message::betweenUsers($coach->id, $user->id)
            ->where('id', '>', $lastMessageId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages from client as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $coach->id)
            ->where('id', '>', $lastMessageId)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $newMessages->map(fn ($msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'is_mine' => $msg->sender_id === $coach->id,
                'created_at' => $msg->created_at->format('M d, g:i A'),
            ]),
        ]);
    }
}
