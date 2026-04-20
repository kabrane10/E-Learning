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
        $this->authorize('create', [ForumPost::class, $topic]);

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
        if (!$topic->isSubscribedBy(Auth::user())) {
            $topic->subscriptions()->create([
                'user_id' => Auth::id(),
                'type' => 'instant',
            ]);
        }

        return redirect()->route('forum.topics.show', [$category->slug, $topic->slug])
            ->with('success', 'Réponse publiée avec succès !');
    }

    public function edit(ForumCategory $category, ForumTopic $topic, ForumPost $post)
    {
        $this->authorize('update', $post);

        return view('forum.posts.edit', compact('category', 'topic', 'post'));
    }

    public function update(Request $request, ForumCategory $category, ForumTopic $topic, ForumPost $post)
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

        return redirect()->route('forum.topics.show', [$category->slug, $topic->slug])
            ->with('success', 'Réponse mise à jour avec succès !');
    }

    public function destroy(ForumCategory $category, ForumTopic $topic, ForumPost $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return back()->with('success', 'Réponse supprimée avec succès !');
    }

    public function like(ForumCategory $category, ForumTopic $topic, ForumPost $post)
    {
        $post->toggleLike(Auth::user());

        return response()->json([
            'success' => true,
            'likes_count' => $post->likes_count,
            'is_liked' => $post->isLikedBy(Auth::user()),
        ]);
    }

    public function markAsSolution(ForumCategory $category, ForumTopic $topic, ForumPost $post)
    {
        $this->authorize('update', $topic);

        // Retirer l'ancienne solution si elle existe
        ForumPost::where('topic_id', $topic->id)
            ->where('is_solution', true)
            ->update(['is_solution' => false]);

        $post->update(['is_solution' => true]);
        $topic->markAsResolved($post);

        // Points bonus pour la solution
        app(\App\Services\GamificationService::class)->addPoints(
            $post->user,
            'solution_marked',
            $post
        );

        return back()->with('success', 'Réponse marquée comme solution !');
    }
}