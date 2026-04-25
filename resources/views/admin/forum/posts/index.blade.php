@extends('layouts.admin')

@section('title', 'Messages du forum')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.categories.index') }}" class="text-gray-400 hover:text-gray-500">Forum</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Messages</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Messages du forum</h1>
            <p class="text-gray-500 mt-1">Gérez tous les messages</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                           class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <select name="user_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous les auteurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="topic_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous les sujets</option>
                        @foreach($topicsList as $t)
                            <option value="{{ $t->id }}" {{ request('topic_id') == $t->id ? 'selected' : '' }}>
                                {{ Str::limit($t->title, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau des messages -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($posts as $post)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $post->user->avatar }}" class="w-8 h-8 rounded-full mr-3">
                                    <span class="text-sm text-gray-900">{{ $post->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 line-clamp-2">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.forum.topics.show', $post->topic) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                                    {{ Str::limit($post->topic->title, 30) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $post->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.forum.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun message trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection