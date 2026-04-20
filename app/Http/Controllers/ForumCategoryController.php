<?php

namespace App\Http\Controllers;

use App\Models\ForumPost; 
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ForumCategory::where('is_active', true)
            ->orderBy('order')
            ->withCount('topics')
            ->with(['topics' => function ($query) {
                $query->orderBy('is_sticky', 'desc')
                      ->orderBy('last_post_at', 'desc')
                      ->limit(5);
            }])
            ->get();

        return view('forum.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', ForumCategory::class);
        
        return view('forum.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ForumCategory::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:forum_categories',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $category = ForumCategory::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'folder',
            'color' => $validated['color'] ?? 'indigo',
            'order' => $validated['order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('forum.categories.show', $category)
            ->with('success', 'Catégorie créée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ForumCategory $category)
    {
        $query = $category->topics()
            ->with(['user', 'lastPostUser'])
            ->withCount('posts');

        // Filtres
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

        // Tri
        $sort = $request->get('sort', 'latest');
        
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'most_replied':
                $query->orderBy('posts_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('is_sticky', 'desc')
                      ->orderBy('last_post_at', 'desc');
        }

        $topics = $query->paginate(20);

        // Statistiques de la catégorie
        $stats = [
            'topics_count' => $category->topics()->count(),
            'posts_count' => $category->topics()->sum('posts_count'),
            'resolved_count' => $category->topics()->where('status', 'resolved')->count(),
        ];

        // Vérifier l'abonnement
        $isSubscribed = Auth::check() && $category->isSubscribedBy(Auth::user());

        return view('forum.categories.show', compact('category', 'topics', 'stats', 'isSubscribed', 'sort'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ForumCategory $category)
    {
        $this->authorize('update', $category);

        return view('forum.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ForumCategory $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:forum_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'] ?? $category->description,
            'icon' => $validated['icon'] ?? $category->icon,
            'color' => $validated['color'] ?? $category->color,
            'order' => $validated['order'] ?? $category->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('forum.categories.show', $category)
            ->with('success', 'Catégorie mise à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ForumCategory $category)
    {
        $this->authorize('delete', $category);

        // Vérifier s'il y a des sujets
        if ($category->topics()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des sujets.');
        }

        $category->delete();

        return redirect()->route('forum.categories.index')
            ->with('success', 'Catégorie supprimée avec succès !');
    }

    /**
     * Subscribe to a category.
     */
    public function subscribe(ForumCategory $category)
    {
        $subscription = $category->subscriptions()->where('user_id', Auth::id())->first();

        if ($subscription) {
            $subscription->delete();
            $message = 'Vous êtes désabonné de cette catégorie.';
            $subscribed = false;
        } else {
            $category->subscriptions()->create([
                'user_id' => Auth::id(),
                'type' => 'instant',
            ]);
            $message = 'Vous êtes maintenant abonné à cette catégorie.';
            $subscribed = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'subscribed' => $subscribed
        ]);
    }

    /**
     * Get topics for a category (API).
     */
    public function topics(ForumCategory $category, Request $request)
    {
        $topics = $category->topics()
            ->with(['user', 'lastPostUser'])
            ->orderBy('is_sticky', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate(20);

        return response()->json($topics);
    }
}