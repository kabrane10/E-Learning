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
        // Récupérer les catégories avec comptage manuel (compatible SQLite)
        $categories = ForumCategory::select('forum_categories.*')
            ->selectRaw('(SELECT COUNT(*) FROM forum_topics WHERE forum_topics.category_id = forum_categories.id AND forum_topics.deleted_at IS NULL) as topics_count')
            ->orderBy('order')
            ->get();

        return view('admin.forum.categories', compact('categories'));
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

        return view('admin.forum.topics', compact('topics', 'categories'));
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

        return view('admin.forum.posts', compact('posts', 'users', 'topicsList'));
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
}