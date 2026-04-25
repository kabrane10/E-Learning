<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Display categories list.
     */
    public function categories()
{

    $categories = ForumCategory::with(['topics' => function($query) {
            $query->orderBy('last_post_at', 'desc')->limit(1);
        }])
        ->select('forum_categories.*')
        ->selectRaw('(SELECT COUNT(*) FROM forum_topics WHERE forum_topics.category_id = forum_categories.id AND forum_topics.deleted_at IS NULL) as topics_count')
        ->orderBy('order')
        ->get();

    // Ajout du dernier sujet pour chaque catégorie
    foreach ($categories as $category) {
        $category->lastTopic = $category->topics->first();
    }

    return view('admin.forum.categories.index', compact('categories'));
}
     /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:forum_categories',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category = ForumCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'folder',
            'color' => $validated['color'] ?? 'indigo',
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return redirect()->route('admin.forum.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

     /**
     * Show a category.
     */
    public function showCategory(ForumCategory $category)
    {
        $category->loadCount('topics');
        
        return view('admin.forum.categories.show', compact('category'));
    }

    /**
 * Show form to create a category.
 */
    public function createCategory()
    {
        return view('admin.forum.categories.create');
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, ForumCategory $category)
    {
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
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? $category->description,
            'icon' => $validated['icon'] ?? $category->icon,
            'color' => $validated['color'] ?? $category->color,
            'order' => $validated['order'] ?? $category->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.forum.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    
    /**
     * Delete a category.
     */
    public function destroyCategory(ForumCategory $category)
    {
        // Vérifier s'il y a des sujets
        if ($category->topics()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des sujets.');
        }

        $category->delete();

        return redirect()->route('admin.forum.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Toggle category active status.
     */
    public function toggleCategory(ForumCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active
        ]);
    }



    /**
     * Display topics list with filters.
     */
    public function topics(Request $request)
    {
        $query = ForumTopic::with(['user', 'category', 'course']);

        // Comptage manuel des posts pour SQLite
        $query->select('forum_topics.*')
            ->selectRaw('(SELECT COUNT(*) FROM forum_posts WHERE forum_posts.topic_id = forum_topics.id AND forum_posts.deleted_at IS NULL) as posts_count');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $topics = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = ForumCategory::all();

        return view('admin.forum.topics.index', compact('topics', 'categories'));
    }

    /**
     * Display posts list with filters.
     */
    public function posts(Request $request)
    {
        $query = ForumPost::with(['user', 'topic.category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%");
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($request->filled('is_solution')) {
            $query->where('is_solution', $request->boolean('is_solution'));
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Récupérer les utilisateurs qui ont posté (compatible SQLite)
        $users = User::whereIn('id', function ($query) {
            $query->select('user_id')->from('forum_posts')->distinct();
        })->get();
        
        $topicsList = ForumTopic::all();

        return view('admin.forum.posts.index', compact('posts', 'users', 'topicsList'));
    }

    /**
     * Delete a topic.
     */
    public function destroyTopic(ForumTopic $topic)
    {
        $topic->delete();

        return back()->with('success', 'Sujet supprimé avec succès.');
    }

    /**
     * Delete a post.
     */
    public function destroyPost(ForumPost $post)
    {
        $post->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }

    /**
     * Toggle pin status of a topic.
     */
    public function togglePin(ForumTopic $topic)
    {
        $topic->update(['is_sticky' => !$topic->is_sticky]);

        return response()->json([
            'success' => true,
            'is_sticky' => $topic->is_sticky
        ]);
    }

    /**
     * Toggle close status of a topic.
     */
    public function toggleClose(ForumTopic $topic)
    {
        $topic->update(['status' => $topic->status === 'closed' ? 'open' : 'closed']);

        return response()->json([
            'success' => true,
            'status' => $topic->status
        ]);
    }

    /**
     * Mark a post as solution.
     */
    public function markAsSolution(ForumPost $post)
    {
        // Retirer l'ancienne solution
        ForumPost::where('topic_id', $post->topic_id)
            ->where('is_solution', true)
            ->update(['is_solution' => false]);

        $post->update(['is_solution' => true]);
        $post->topic->update(['status' => 'resolved']);

        return response()->json(['success' => true]);
    }

    /**
     * Bulk action on topics.
     */
    public function bulkActionTopics(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:pin,unpin,close,open,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:forum_topics,id',
        ]);

        $topics = ForumTopic::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'pin':
                $topics->update(['is_sticky' => true]);
                $message = 'Sujets épinglés';
                break;
            case 'unpin':
                $topics->update(['is_sticky' => false]);
                $message = 'Sujets désépinglés';
                break;
            case 'close':
                $topics->update(['status' => 'closed']);
                $message = 'Sujets fermés';
                break;
            case 'open':
                $topics->update(['status' => 'open']);
                $message = 'Sujets ouverts';
                break;
            case 'delete':
                $count = $topics->count();
                $topics->delete();
                $message = $count . ' sujet(s) supprimé(s)';
                break;
            default:
                $message = 'Aucune action effectuée';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Bulk action on posts.
     */
    public function bulkActionPosts(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:forum_posts,id',
        ]);

        if ($validated['action'] === 'delete') {
            $count = ForumPost::whereIn('id', $validated['ids'])->count();
            ForumPost::whereIn('id', $validated['ids'])->delete();
            $message = $count . ' message(s) supprimé(s)';
        } else {
            $message = 'Aucune action effectuée';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Display forum statistics.
     */
    public function statistics()
    {
        $stats = [
            'categories_count' => ForumCategory::count(),
            'topics_count' => ForumTopic::count(),
            'posts_count' => ForumPost::count(),
            'topics_today' => ForumTopic::whereDate('created_at', today())->count(),
            'posts_today' => ForumPost::whereDate('created_at', today())->count(),
        ];

        // Données pour les graphiques
        $topicsChartData = $this->getTopicsChartData();
        $categoriesChartData = $this->getCategoriesChartData();

        // Top sujets (compatible SQLite)
        $topTopics = ForumTopic::with(['category', 'user'])
            ->select('forum_topics.*')
            ->selectRaw('(SELECT COUNT(*) FROM forum_posts WHERE forum_posts.topic_id = forum_topics.id AND forum_posts.deleted_at IS NULL) as posts_count')
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.forum.statistics', compact('stats', 'topicsChartData', 'categoriesChartData', 'topTopics'));
    }

    /**
     * Get topics chart data (last 30 days).
     */
    private function getTopicsChartData(): array
    {
        $data = ForumTopic::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Si pas de données, retourner des données par défaut
        if ($data->isEmpty()) {
            return [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [0, 0, 0, 0, 0, 0, 0],
            ];
        }

        return [
            'labels' => $data->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get categories chart data (compatible SQLite).
     */
    private function getCategoriesChartData(): array
    {
        // Récupérer toutes les catégories
        $categories = ForumCategory::all();
        
        // Compter manuellement les sujets pour chaque catégorie
        $data = $categories->map(function ($category) {
            $category->topics_count = ForumTopic::where('category_id', $category->id)->count();
            return $category;
        })
        ->filter(function ($category) {
            return $category->topics_count > 0;
        })
        ->sortByDesc('topics_count')
        ->take(6);
        
        // Si pas de données, retourner des données par défaut
        if ($data->isEmpty()) {
            return [
                'labels' => ['Aucun sujet'],
                'data' => [1],
            ];
        }

        return [
            'labels' => $data->pluck('name')->toArray(),
            'data' => $data->pluck('topics_count')->toArray(),
        ];
    }
    
     /**
     * Display admin forum dashboard.
     */
    public function index()
    {
        $stats = [
            'categories_count' => ForumCategory::count(),
            'topics_count' => ForumTopic::count(),
            'posts_count' => ForumPost::count(),
            'topics_today' => ForumTopic::whereDate('created_at', today())->count(),
        ];
        
        $recentTopics = ForumTopic::with(['user', 'category'])
            ->latest()
            ->limit(10)
            ->get();
            
        $recentPosts = ForumPost::with(['user', 'topic'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.forum.index', compact('stats', 'recentTopics', 'recentPosts'));
    }

    /**
     * Show a specific topic (admin view).
     */
    public function showTopic(ForumTopic $topic)
    {
        $topic->load(['user', 'category', 'course']);
        
        $posts = $topic->posts()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'asc')
            ->paginate(30);
        
        $stats = [
            'views' => $topic->views_count,
            'posts' => $topic->posts_count,
            'likes' => $topic->likes_count,
            'subscribers' => $topic->subscriptions()->count(),
        ];
        
        return view('admin.forum.topics.show', compact('topic', 'posts', 'stats'));
    }

    /**
     * Show form to edit a topic.
     */
    public function editTopic(ForumTopic $topic)
    {
        $categories = ForumCategory::where('is_active', true)->get();
        
        return view('admin.forum.topics.edit', compact('topic', 'categories'));
    }

    /**
     * Update a topic.
     */
    public function updateTopic(Request $request, ForumTopic $topic)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:forum_categories,id',
            'type' => 'required|in:general,question,announcement,resource',
            'status' => 'required|in:open,closed,resolved',
            'is_sticky' => 'boolean',
        ]);

        $topic->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'is_sticky' => $request->boolean('is_sticky'),
        ]);

        // Mettre à jour le slug si le titre a changé
        if ($topic->wasChanged('title')) {
            $topic->update(['slug' => Str::slug($validated['title']) . '-' . Str::random(6)]);
        }

        return redirect()->route('admin.forum.topics.show', $topic)
            ->with('success', 'Sujet mis à jour avec succès.');
    }

    /**
     * Store a reply from admin.
     */
    public function storePost(Request $request, ForumTopic $topic)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:5',
            'parent_id' => 'nullable|exists:forum_posts,id',
        ]);

        $post = $topic->posts()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Mettre à jour le topic
        $topic->update([
            'last_post_at' => now(),
            'last_post_user_id' => Auth::id(),
        ]);
        $topic->increment('posts_count');

        // Points pour l'admin (optionnel)
        if (class_exists(\App\Services\GamificationService::class)) {
            try {
                app(\App\Services\GamificationService::class)->addPoints(
                    Auth::user(),
                    'forum_post_created',
                    $post
                );
            } catch (\Exception $e) {
                \Log::error('Erreur gamification admin post: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.forum.topics.show', $topic)
            ->with('success', 'Réponse publiée avec succès.');
    }

    /**
     * Show form to edit a post.
     */
    public function editPost(ForumPost $post)
    {
        return view('admin.forum.posts.edit', compact('post'));
    }

    /**
     * Update a post.
     */
    public function updatePost(Request $request, ForumPost $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $post->update([
            'content' => $validated['content'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return redirect()->route('admin.forum.topics.show', $post->topic)
            ->with('success', 'Message mis à jour avec succès.');
    }
    

/**
 * Show form to edit a category.
 */
public function editCategory(ForumCategory $category)
{
    return view('admin.forum.categories.edit', compact('category'));
}

/**
 * Show form to create a topic.
 */
public function createTopic()
{
    $categories = ForumCategory::where('is_active', true)->get();
    return view('admin.forum.topics.create', compact('categories'));
}

/**
 * Store a new topic.
 */
public function storeTopic(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_id' => 'required|exists:forum_categories,id',
        'type' => 'required|in:general,question,announcement,resource',
        'is_sticky' => 'boolean',
        'is_announcement' => 'boolean',
    ]);

    $topic = ForumTopic::create([
        'title' => $validated['title'],
        'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
        'content' => $validated['content'],
        'category_id' => $validated['category_id'],
        'user_id' => Auth::id(),
        'type' => $validated['type'],
        'is_sticky' => $request->boolean('is_sticky'),
        'is_announcement' => $request->boolean('is_announcement'),
        'last_post_at' => now(),
    ]);

    return redirect()->route('admin.forum.topics.show', $topic)
        ->with('success', 'Sujet créé avec succès.');
}
}