<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ForumTopicController extends Controller
{
    /**
     * Constructor with authorization middleware.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the topics.
     */
    public function index(Request $request, ForumCategory $category = null)
    {
        $query = ForumTopic::with(['user', 'lastPostUser', 'category'])
            ->withCount('posts');

        if ($category) {
            $query->where('category_id', $category->id);
        }

        if ($request->has('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Trier : épinglés d'abord, puis par dernière activité
        $query->orderBy('is_sticky', 'desc')
              ->orderBy('is_announcement', 'desc')
              ->orderBy('last_post_at', 'desc');

        $topics = $query->paginate(20);
        $categories = ForumCategory::where('is_active', true)->get();

        return view('forum.topics.index', compact('topics', 'category', 'categories'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create(Request $request)
    {
        $categories = ForumCategory::where('is_active', true)->get();
        $course = null;
        
        if ($request->has('course_id')) {
            $course = Course::find($request->course_id);
        }

        return view('forum.topics.create', compact('categories', 'course'));
    }

    /**
     * Store a newly created topic.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:forum_categories,id',
            'course_id' => 'nullable|exists:courses,id',
            'type' => 'required|in:general,question,announcement,resource',
        ]);

        $topic = ForumTopic::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'course_id' => $validated['course_id'] ?? null,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'last_post_at' => now(),
        ]);

        // Abonner automatiquement l'auteur
        if (method_exists($topic, 'subscriptions')) {
            $topic->subscriptions()->create([
                'user_id' => Auth::id(),
                'type' => 'instant',
            ]);
        }

        return redirect()->route('forum.topics.show', $topic)
            ->with('success', 'Sujet créé avec succès !');
    }

    /**
     * Display the specified topic.
     * Utilise le Route Model Binding automatique.
     */
    public function show(ForumTopic $topic)
    {
        $topic->increment('views_count');
        
        $category = $topic->category;

        $posts = $topic->posts()
            ->with(['user', 'replies.user', 'likes'])
            ->get();

        $isSubscribed = Auth::check() && method_exists($topic, 'isSubscribedBy') 
            ? $topic->isSubscribedBy(Auth::user()) 
            : false;

        return view('forum.topics.show', compact('topic', 'category', 'posts', 'isSubscribed'));
    }

    /**
     * Show the form for editing the specified topic.
     */
    public function edit(ForumTopic $topic)
    {
        $this->authorize('update', $topic);

        $category = $topic->category;
        $categories = ForumCategory::where('is_active', true)->get();

        return view('forum.topics.edit', compact('topic', 'category', 'categories'));
    }

    /**
     * Update the specified topic.
     */
    public function update(Request $request, ForumTopic $topic)
    {
        $this->authorize('update', $topic);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:forum_categories,id',
            'type' => 'required|in:general,question,announcement,resource',
        ]);

        $topic->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
        ]);

        // Mettre à jour le slug si le titre a changé
        if ($topic->wasChanged('title')) {
            $topic->update(['slug' => Str::slug($validated['title']) . '-' . Str::random(6)]);
        }

        return redirect()->route('forum.topics.show', $topic)
            ->with('success', 'Sujet mis à jour avec succès !');
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(ForumTopic $topic)
    {
        $this->authorize('delete', $topic);

        $topic->delete();

        return redirect()->route('forum.index')
            ->with('success', 'Sujet supprimé avec succès !');
    }

    /**
     * Toggle pin status.
     */
    public function togglePin(ForumTopic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update(['is_sticky' => !$topic->is_sticky]);

        return back()->with('success', $topic->is_sticky ? 'Sujet épinglé !' : 'Sujet désépinglé !');
    }

    /**
     * Toggle close status.
     */
    public function toggleClose(ForumTopic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update(['status' => $topic->status === 'closed' ? 'open' : 'closed']);

        return back()->with('success', $topic->status === 'closed' ? 'Sujet fermé !' : 'Sujet rouvert !');
    }

    /**
     * Subscribe to a topic.
     */
    public function subscribe(ForumTopic $topic)
    {
        if (!method_exists($topic, 'subscriptions')) {
            return response()->json(['success' => false, 'message' => 'Fonctionnalité non disponible']);
        }

        $subscription = $topic->subscriptions()->where('user_id', Auth::id())->first();

        if ($subscription) {
            $subscription->delete();
            $message = 'Vous êtes désabonné de ce sujet.';
            $subscribed = false;
        } else {
            $topic->subscriptions()->create([
                'user_id' => Auth::id(),
                'type' => 'instant',
            ]);
            $message = 'Vous êtes maintenant abonné à ce sujet.';
            $subscribed = true;
        }

        return response()->json(['success' => true, 'message' => $message, 'subscribed' => $subscribed]);
    }
}