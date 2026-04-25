<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, ForumCategory $category, ForumTopic $topic)
    {
        
       // Vérifier si le sujet est fermé
       if ($topic->status === 'closed') {
        return back()->with('error', 'Ce sujet est fermé. Vous ne pouvez plus répondre.');
    }

    $validated = $request->validate([
        'content' => 'required|string|min:5',
        'parent_id' => 'nullable|exists:forum_posts,id',
    ]);

    $post = $topic->posts()->create([
        'user_id' => Auth::id(),
        'content' => $validated['content'],
        'parent_id' => $validated['parent_id'] ?? null,
    ]);

    // Abonner automatiquement l'auteur de la réponse
    if (method_exists($topic, 'subscriptions') && !$topic->isSubscribedBy(Auth::user())) {
        $topic->subscriptions()->create([
            'user_id' => Auth::id(),
            'type' => 'instant',
        ]);
    }

    // Redirection avec ancre vers le nouveau message
    return redirect()
        ->route('forum.topics.show', $topic)
        ->with('success', 'Réponse publiée avec succès !')
        ->withFragment('post-' . $post->id);
}

    /**
     * Show the form for editing the specified post.
     */
    public function edit(ForumPost $post)
    {
        $this->authorize('update', $post);
        
        $topic = $post->topic;
        $category = $topic->category;

        return view('forum.posts.edit', compact('category', 'topic', 'post'));
    }

    public function update(Request $request, ForumPost $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $post->update([
            'content' => $validated['content'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('forum.topics.show', $post->topic)
            ->with('success', 'Réponse mise à jour avec succès !')
            ->withFragment('post-' . $post->id);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(ForumPost $post)
    {
        $this->authorize('delete', $post);

        $topic = $post->topic;
        $post->delete();

        return redirect()
            ->route('forum.topics.show', $topic)
            ->with('success', 'Réponse supprimée avec succès !');
    }

     /**
     * Like/Unlike a post.
     */
    public function like(ForumPost $post)
    {
        if (!method_exists($post, 'toggleLike')) {
            return response()->json(['success' => false, 'message' => 'Fonction non disponible']);
        }
        
        $post->toggleLike(Auth::user());

        return response()->json([
            'success' => true,
            'likes_count' => $post->likes_count,
            'is_liked' => $post->isLikedBy(Auth::user()),
        ]);
    }


    /**
     * Mark a post as solution.
     */
    public function markAsSolution(ForumTopic $topic, ForumPost $post)
    {
        $this->authorize('update', $topic);

        // Retirer l'ancienne solution si elle existe
        ForumPost::where('topic_id', $topic->id)
            ->where('is_solution', true)
            ->update(['is_solution' => false]);

        $post->update(['is_solution' => true]);
        $topic->markAsResolved($post);

        // Points bonus pour la solution
        if (class_exists(\App\Services\GamificationService::class)) {
            try {
                app(\App\Services\GamificationService::class)->addPoints(
                    $post->user,
                    'solution_marked',
                    $post
                );
            } catch (\Exception $e) {
                \Log::error('Erreur gamification solution: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Réponse marquée comme solution !');
    }
}