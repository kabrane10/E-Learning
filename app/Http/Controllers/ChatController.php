<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $conversations = $user->conversations()
            ->with(['lastMessage.user', 'participants.user'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                $conversation->unread_count = $conversation->unreadMessagesForUser($user->id);
                
                if ($conversation->type === 'private') {
                    $otherParticipant = $conversation->participants
                        ->where('user_id', '!=', $user->id)
                        ->first();
                    $conversation->other_user = $otherParticipant ? $otherParticipant->user : null;
                    $conversation->title = $conversation->other_user->name ?? 'Conversation';
                }
                
                return $conversation;
            });

        $contacts = User::where('id', '!=', $user->id)
            ->whereIn('id', function ($query) use ($user) {
                $query->select('user_id')
                    ->from('participants')
                    ->whereIn('conversation_id', function ($subQuery) use ($user) {
                        $subQuery->select('conversation_id')
                            ->from('participants')
                            ->where('user_id', $user->id);
                    });
            })
            ->get();

        $availableUsers = User::where('id', '!=', $user->id)
            ->whereNotIn('id', $contacts->pluck('id'))
            ->limit(20)
            ->get();

        return view('chat.index', compact('conversations', 'contacts', 'availableUsers'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $user = Auth::user();
        
        $messages = $conversation->messages()
            ->with(['user', 'replyTo.user'])
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $conversation->markAsRead($user->id);

        if ($conversation->type === 'private') {
            $otherParticipant = $conversation->participants
                ->where('user_id', '!=', $user->id)
                ->first();
            $conversation->other_user = $otherParticipant ? $otherParticipant->user : null;
        }

        return view('chat.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'nullable|string|in:text,image,file',
            'reply_to_id' => 'nullable|exists:messages,id',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'text',
            'reply_to_id' => $validated['reply_to_id'] ?? null,
        ]);

        $conversation->markAsRead(Auth::id());

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('user')
        ]);
    }

    public function markAsRead(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $validated = $request->validate([
            'message_ids' => 'nullable|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        $conversation->markAsRead(Auth::id());

        if (!empty($validated['message_ids'])) {
            Message::whereIn('id', $validated['message_ids'])
                ->where('conversation_id', $conversation->id)
                ->update(['read_at' => now()]);

            broadcast(new MessageRead(
                $conversation->id,
                Auth::id(),
                $validated['message_ids']
            ))->toOthers();
        }

        return response()->json(['success' => true]);
    }

    public function typing(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $validated = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        broadcast(new UserTyping(
            $conversation->id,
            Auth::id(),
            Auth::user()->name,
            $validated['is_typing']
        ))->toOthers();

        return response()->json(['success' => true]);
    }

    public function uploadFile(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('chat/' . $conversation->id, 'public');
        
        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $file->getClientOriginalName(),
            'type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file',
            'metadata' => [
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ],
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('user')
        ]);
    }
}