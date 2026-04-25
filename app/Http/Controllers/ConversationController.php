<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conversations = Auth::user()->conversations()
            ->with(['lastMessage.user', 'participants.user'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('conversations.index', compact('conversations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $courses = Course::where('is_published', true)->get();
        
        return view('conversations.create', compact('users', 'courses'));
    }

   /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation conditionnelle selon le type
        $rules = [
            'type' => ['required', Rule::in(['private', 'course', 'group'])],
        ];

        // Ajouter les règles conditionnelles
        if ($request->input('type') === 'private') {
            $rules['user_id'] = ['required', 'exists:users,id'];
            $rules['course_id'] = ['nullable'];
            $rules['title'] = ['nullable'];
        } elseif ($request->input('type') === 'course') {
            $rules['user_id'] = ['nullable'];
            $rules['course_id'] = ['required', 'exists:courses,id'];
            $rules['title'] = ['nullable'];
        } elseif ($request->input('type') === 'group') {
            $rules['user_id'] = ['nullable'];
            $rules['course_id'] = ['nullable'];
            $rules['title'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        \Log::info('Création de conversation - Données validées', $validated);

        // Vérifier si une conversation privée existe déjà
        if ($validated['type'] === 'private' && isset($validated['user_id'])) {
            $existingConversation = Conversation::where('type', 'private')
                ->whereHas('participants', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->whereHas('participants', function ($q) use ($validated) {
                    $q->where('user_id', $validated['user_id']);
                })
                ->first();

            if ($existingConversation) {
                \Log::info('Conversation existante trouvée', ['id' => $existingConversation->id]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('chat.show', $existingConversation),
                        'conversation' => $existingConversation
                    ]);
                }
                
                return redirect()->route('chat.show', $existingConversation);
            }
        }

        // Créer la nouvelle conversation
        $conversationData = [
            'type' => $validated['type'],
            'created_by' => Auth::id(),
            'last_message_at' => now(),
        ];

        // Ajouter les champs optionnels
        if (isset($validated['course_id']) && $validated['course_id']) {
            $conversationData['course_id'] = $validated['course_id'];
        }
        
        if (isset($validated['title']) && $validated['title']) {
            $conversationData['title'] = $validated['title'];
        }

        $conversation = Conversation::create($conversationData);

        \Log::info('Conversation créée', ['id' => $conversation->id, 'data' => $conversationData]);

        // Ajouter le créateur comme participant admin
        $conversation->participants()->create([
            'user_id' => Auth::id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        // Ajouter l'autre utilisateur pour les conversations privées
        if ($validated['type'] === 'private' && isset($validated['user_id'])) {
            $conversation->participants()->create([
                'user_id' => $validated['user_id'],
                'role' => 'member',
                'joined_at' => now(),
            ]);
            
            // Définir un titre par défaut pour les conversations privées (optionnel)
            $otherUser = User::find($validated['user_id']);
            if ($otherUser) {
                $conversation->update(['title' => 'Conversation avec ' . $otherUser->name]);
            }
        }

        // Pour les conversations de cours, ajouter le formateur
        if ($validated['type'] === 'course' && isset($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            if ($course && $course->instructor_id) {
                // Vérifier si le formateur n'est pas déjà le créateur
                if ($course->instructor_id !== Auth::id()) {
                    $conversation->participants()->create([
                        'user_id' => $course->instructor_id,
                        'role' => 'admin',
                        'joined_at' => now(),
                    ]);
                }
            }
        }

        // Message système de bienvenue
        $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => 'Conversation créée',
            'type' => 'system',
        ]);

        \Log::info('Participants ajoutés');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('chat.show', $conversation),
                'conversation' => $conversation
            ]);
        }

        return redirect()->route('chat.show', $conversation)
            ->with('success', 'Conversation créée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['participants.user', 'course']);
        
        return view('conversations.show', compact('conversation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $conversation->update($validated);

        return back()->with('success', 'Conversation mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return redirect()->route('chat.index')
            ->with('success', 'Conversation supprimée.');
    }

    /**
     * Add participants to a conversation.
     */
    public function addParticipants(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            // Vérifier si le participant existe déjà
            $exists = $conversation->participants()->where('user_id', $userId)->exists();
            
            if (!$exists) {
                $conversation->participants()->create([
                    'user_id' => $userId,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Participants ajoutés.');
    }

    /**
     * Remove a participant from a conversation.
     */
    public function removeParticipant(Conversation $conversation, User $user)
    {
        $this->authorize('update', $conversation);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas vous retirer vous-même. Utilisez "Quitter".');
        }

        $conversation->participants()->where('user_id', $user->id)->delete();

        return back()->with('success', 'Participant retiré.');
    }

    /**
     * Leave a conversation.
     */
    public function leave(Conversation $conversation)
    {
        $conversation->participants()->where('user_id', Auth::id())->delete();

        return redirect()->route('chat.index')
            ->with('success', 'Vous avez quitté la conversation.');
    }

    /**
     * Toggle mute status.
     */
    public function toggleMute(Conversation $conversation)
    {
        $participant = $conversation->participants()
            ->where('user_id', Auth::id())
            ->first();

        if ($participant) {
            $participant->update(['is_muted' => !$participant->is_muted]);
            
            return response()->json([
                'success' => true,
                'is_muted' => $participant->is_muted
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Toggle pin status.
     */
    public function togglePin(Conversation $conversation)
    {
        $participant = $conversation->participants()
            ->where('user_id', Auth::id())
            ->first();

        if ($participant) {
            $participant->update(['is_pinned' => !$participant->is_pinned]);
            
            return response()->json([
                'success' => true,
                'is_pinned' => $participant->is_pinned
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}