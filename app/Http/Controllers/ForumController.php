<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;  // ← AJOUTER CETTE LIGNE
use App\Models\User;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::where('is_active', true)
            ->orderBy('order')
            ->with(['topics' => function ($query) {
                $query->where('status', '!=', 'closed')
                    ->orderBy('is_sticky', 'desc')
                    ->orderBy('last_post_at', 'desc')
                    ->limit(5);
            }])
            ->get();

        $recentTopics = ForumTopic::with(['user', 'category', 'lastPostUser'])
            ->where('status', '!=', 'closed')
            ->orderBy('last_post_at', 'desc')
            ->limit(10)
            ->get();

        $popularTopics = ForumTopic::with(['user', 'category'])
            ->where('status', '!=', 'closed')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'topics_count' => ForumTopic::count(),
            'posts_count' => ForumPost::count(),
            'users_count' => User::count(),
            'online_users' => User::where('last_activity_at', '>=', now()->subMinutes(5))->count(),
        ];

        return view('forum.index', compact('categories', 'recentTopics', 'popularTopics', 'stats'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $topics = ForumTopic::with(['user', 'category'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->orderBy('last_post_at', 'desc')
            ->paginate(20);

        return view('forum.search', compact('topics', 'query'));
    }
}