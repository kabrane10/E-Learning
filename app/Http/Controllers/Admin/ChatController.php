<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display chat dashboard.
     */
    public function index()
    {
        $stats = [
            'total_conversations' => Conversation::count(),
            'private_conversations' => Conversation::where('type', 'private')->count(),
            'course_conversations' => Conversation::where('type', 'course')->count(),
            'group_conversations' => Conversation::where('type', 'group')->count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'active_conversations' => Conversation::where('last_message_at', '>=', now()->subDays(7))->count(),
            'total_participants' => DB::table('participants')->count(),
        ];

        $recentConversations = Conversation::with(['creator', 'course'])
            ->withCount(['messages', 'participants'])
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();

        $activeUsers = $this->getActiveUsers();

        $dailyActivity = Message::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.chat.index', compact('stats', 'recentConversations', 'activeUsers', 'dailyActivity'));
    }

    /**
     * Get active users (compatible SQLite).
     */
    private function getActiveUsers()
    {
        $users = User::whereIn('id', function ($query) {
                $query->select('user_id')
                    ->from('messages')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->whereNull('deleted_at')
                    ->distinct();
            })
            ->get();
        
        foreach ($users as $user) {
            $user->messages_count = Message::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
        }
        
        return $users->sortByDesc('messages_count')->take(10)->values();
    }

    /**
     * Display all conversations.
     */
    public function conversations(Request $request)
    {
        $query = Conversation::with(['creator', 'course']);
        
        $query->select('conversations.*')
            ->selectRaw('(SELECT COUNT(*) FROM messages WHERE messages.conversation_id = conversations.id AND messages.deleted_at IS NULL) as messages_count')
            ->selectRaw('(SELECT COUNT(*) FROM participants WHERE participants.conversation_id = conversations.id) as participants_count');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('creator', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $conversations = $query->orderBy('last_message_at', 'desc')->paginate(20);

        return view('admin.chat.conversations', compact('conversations'));
    }

    /**
     * Show form to create a conversation.
     */
    public function createConversation()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $courses = Course::where('is_published', true)->get();
        
        return view('admin.chat.conversations.create', compact('users', 'courses'));
    }

    /**
     * Store a new conversation.
     */
    public function storeConversation(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:private,course,group',
            'user_id' => 'required_if:type,private|exists:users,id',
            'course_id' => 'required_if:type,course|exists:courses,id',
            'title' => 'required_if:type,group|string|max:255',
        ]);

        $conversation = Conversation::create([
            'type' => $validated['type'],
            'course_id' => $validated['course_id'] ?? null,
            'title' => $validated['title'] ?? null,
            'created_by' => auth()->id(),
            'last_message_at' => now(),
        ]);

        $conversation->participants()->create([
            'user_id' => auth()->id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        if ($validated['type'] === 'private' && isset($validated['user_id'])) {
            $conversation->participants()->create([
                'user_id' => $validated['user_id'],
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }

        if ($validated['type'] === 'course' && isset($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            if ($course && $course->instructor_id) {
                $conversation->participants()->create([
                    'user_id' => $course->instructor_id,
                    'role' => 'admin',
                    'joined_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.chat.conversations.show', $conversation)
            ->with('success', 'Conversation créée avec succès !');
    }

    /**
     * Show a specific conversation.
     */
    public function showConversation(Conversation $conversation)
    {
        $conversation->load(['creator', 'course', 'participants.user']);
        
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $stats = [
            'total_messages' => $conversation->messages()->count(),
            'total_participants' => $conversation->participants()->count(),
            'first_message_at' => $conversation->messages()->min('created_at'),
            'last_message_at' => $conversation->last_message_at,
        ];

        return view('admin.chat.conversations.show', compact('conversation', 'messages', 'stats'));
    }

    /**
     * Show form to edit a conversation.
     */
    public function editConversation(Conversation $conversation)
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $courses = Course::where('is_published', true)->get();
        
        return view('admin.chat.conversations.edit', compact('conversation', 'users', 'courses'));
    }

    /**
     * Update a conversation.
     */
    public function updateConversation(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $conversation->update($validated);

        return redirect()->route('admin.chat.conversations.show', $conversation)
            ->with('success', 'Conversation mise à jour avec succès !');
    }

    /**
     * Delete a conversation.
     */
    public function destroyConversation(Conversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('admin.chat.conversations.index')
            ->with('success', 'Conversation supprimée avec succès.');
    }

    /**
     * Display all messages.
     */
    public function messages(Request $request)
    {
        $query = Message::with(['user', 'conversation']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('conversation_id')) {
            $query->where('conversation_id', $request->conversation_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%");
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(30);
        
        $users = User::whereIn('id', function ($q) {
            $q->select('user_id')->from('messages')->distinct();
        })->get();
        
        $conversations = Conversation::all();

        return view('admin.chat.messages', compact('messages', 'users', 'conversations'));
    }

    /**
     * Delete a message.
     */
    public function destroyMessage(Message $message)
    {
        $message->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }

    /**
     * Display chat settings.
     */
    public function settings()
    {
        return view('admin.chat.settings');
    }

    /**
     * Update chat settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_message_length' => 'required|integer|min:100|max:5000',
            'enable_file_upload' => 'boolean',
            'max_file_size' => 'required|integer|min:1|max:50',
            'enable_typing_indicator' => 'boolean',
            'enable_read_receipts' => 'boolean',
            'message_retention_days' => 'required|integer|min:1|max:365',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\Setting::set('chat_' . $key, $value);
        }

        return back()->with('success', 'Paramètres du chat mis à jour avec succès.');
    }

    /**
     * Export chat data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $data = [
            'stats' => [
                'total_conversations' => Conversation::count(),
                'total_messages' => Message::count(),
                'active_users' => User::whereIn('id', fn($q) => $q->select('user_id')->from('messages')->distinct())->count(),
            ],
            'exported_at' => now()->toISOString(),
        ];

        if ($format === 'json') {
            return response()->json($data, 200, [
                'Content-Disposition' => 'attachment; filename="chat-stats.json"'
            ]);
        }

        $filename = 'chat-stats-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Métrique', 'Valeur']);
            fputcsv($file, ['Total conversations', $data['stats']['total_conversations']]);
            fputcsv($file, ['Total messages', $data['stats']['total_messages']]);
            fputcsv($file, ['Utilisateurs actifs', $data['stats']['active_users']]);
            fputcsv($file, ['Exporté le', $data['exported_at']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}