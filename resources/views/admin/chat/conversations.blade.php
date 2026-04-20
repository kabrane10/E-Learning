@extends('layouts.admin')

@section('title', 'Conversations')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Conversations</h1>
                <p class="text-gray-500 mt-1">Gérez toutes les conversations de la plateforme</p>
            </div>
            <a href="{{ route('admin.chat.index') }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Privée</option>
                        <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>Cours</option>
                        <option value="group" {{ request('type') == 'group' ? 'selected' : '' }}>Groupe</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre ou créateur..."
                           class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des conversations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre/Cours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière activité</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($conversations as $conversation)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 text-sm text-gray-500">#{{ $conversation->id }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $conversation->type === 'private' ? 'bg-blue-100 text-blue-700' : 
                                       ($conversation->type === 'course' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700') }}">
                                    {{ ucfirst($conversation->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($conversation->type === 'course' && $conversation->course)
                                    <a href="{{ route('admin.courses.show', $conversation->course) }}" class="text-indigo-600 hover:text-indigo-700">
                                        {{ $conversation->course->title }}
                                    </a>
                                @else
                                    {{ $conversation->title ?? 'Sans titre' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $conversation->creator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $conversation->messages_count }}</td>
                            <td class="px-6 py-4">{{ $conversation->participants_count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.chat.conversations.show', $conversation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.chat.conversations.destroy', $conversation) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Supprimer cette conversation ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Aucune conversation trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
    </div>
</div>
@endsection