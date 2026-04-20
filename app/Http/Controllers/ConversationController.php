<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Conversation::class, 'conversation');
    }

    public function index()
    {
        $conversations = Auth::user()->conversations()
            ->with(['lastMessage.user', 'participants.user'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('conversations.index', compact('conversations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required_without:course_id|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:private,course,group',
        ]);

        // Vérifier si une conversation privée existe déjà
        if ($validated['type'] === 'private') {
            $existingConversation = Conversation::where('type', 'private')
                ->whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
                ->whereHas('participants', fn($q) => $q->where('user_id', $validated['user_id']))
                ->first();

            if ($existingConversation) {
                return redirect()->route('chat.show', $existingConversation);
            }
        }

        $conversation = Conversation::create([
            'type' => $validated['type'],
            'course_id' => $validated['course_id'] ?? null,
            'title' => $validated['title'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Ajouter le créateur comme participant
        $conversation->addParticipant(Auth::id(), 'admin');

        // Ajouter l'autre utilisateur pour les conversations privées
        if ($validated['type'] === 'private') {
            $conversation->addParticipant($validated['user_id']);
        }

        // Pour les conversations de cours, ajouter tous les étudiants
        if ($validated['type'] === 'course' && $validated['course_id']) {
            $course = Course::find($validated['course_id']);
            foreach ($course->students as $student) {
                $conversation->addParticipant($student->id);
            }
            $conversation->addParticipant($course->instructor_id, 'admin');
        }

        // Message système de bienvenue
        $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => 'Conversation créée',
            'type' => 'system',
        ]);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        return redirect()->route('chat.show', $conversation);
    }

    public function update(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $conversation->update($validated);

        return back()->with('success', 'Conversation mise à jour.');
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('chat.index')
            ->with('success', 'Conversation supprimée.');
    }

    public function addParticipants(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            $conversation->addParticipant($userId);
        }

        return back()->with('success', 'Participants ajoutés.');
    }

    public function removeParticipant(Conversation $conversation, User $user)
    {
        $this->authorize('update', $conversation);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas vous retirer vous-même.');
        }

        $conversation->removeParticipant($user->id);

        return back()->with('success', 'Participant retiré.');
    }

    public function leave(Conversation $conversation)
    {
        $conversation->removeParticipant(Auth::id());

        return redirect()->route('chat.index')
            ->with('success', 'Vous avez quitté la conversation.');
    }

    public function toggleMute(Conversation $conversation)
    {
        $participant = $conversation->participants()
            ->where('user_id', Auth::id())
            ->first();

        if ($participant) {
            $participant->update(['is_muted' => !$participant->is_muted]);
        }

        return response()->json([
            'success' => true,
            'is_muted' => $participant->is_muted
        ]);
    }

    public function togglePin(Conversation $conversation)
    {
        $participant = $conversation->participants()
            ->where('user_id', Auth::id())
            ->first();

        if ($participant) {
            $participant->update(['is_pinned' => !$participant->is_pinned]);
        }

        return response()->json([
            'success' => true,
            'is_pinned' => $participant->is_pinned
        ]);
    }
}