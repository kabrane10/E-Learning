@extends('layouts.admin')

@section('title', 'Sujets du forum')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sujets du forum</h1>
                <p class="text-gray-500 mt-1">Gérez tous les sujets de discussion</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.forum.categories.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-folder mr-2"></i>Catégories
                </a>
                <a href="{{ route('admin.forum.statistics') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </a>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre ou contenu..."
                           class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catégorie</label>
                    <select name="category_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Toutes</option>
                        @foreach(\App\Models\ForumCategory::all() as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>Général</option>
                        <option value="question" {{ request('type') == 'question' ? 'selected' : '' }}>Question</option>
                        <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Annonce</option>
                        <option value="resource" {{ request('type') == 'resource' ? 'selected' : '' }}>Ressource</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Fermé</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Résolu</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.forum.topics.index') }}" class="ml-2 px-4 py-2 text-gray-600 hover:text-gray-900">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des sujets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <input type="checkbox" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Réponses</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vues</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topics as $topic)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300" value="{{ $topic->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <div class="flex items-center space-x-1 mb-1">
                                            @if($topic->is_sticky)
                                                <i class="fas fa-thumbtack text-indigo-500 text-xs"></i>
                                            @endif
                                            @if($topic->is_announcement)
                                                <span class="text-xs text-red-500">
                                                    <i class="fas fa-bullhorn"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('forum.topics.show', [$topic->category->slug ?? 'general', $topic->slug]) }}" 
                                           target="_blank"
                                           class="font-medium text-gray-900 hover:text-indigo-600 line-clamp-1">
                                            {{ $topic->title }}
                                        </a>
                                        @if($topic->course)
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-book mr-1"></i>{{ $topic->course->title }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $topic->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($topic->user->name) }}" 
                                             class="w-6 h-6 rounded-full mr-2">
                                        <span class="text-sm text-gray-900">{{ $topic->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ $topic->category->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $topic->type === 'question' ? 'bg-yellow-100 text-yellow-700' : 
                                           ($topic->type === 'announcement' ? 'bg-red-100 text-red-700' : 
                                           ($topic->type === 'resource' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700')) }}">
                                        {{ ucfirst($topic->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $topic->status === 'open' ? 'bg-green-100 text-green-700' : 
                                           ($topic->status === 'resolved' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        @if($topic->status === 'open')
                                            <i class="fas fa-circle text-green-500 text-xs mr-1"></i>Ouvert
                                        @elseif($topic->status === 'resolved')
                                            <i class="fas fa-check-circle mr-1"></i>Résolu
                                        @else
                                            <i class="fas fa-lock mr-1"></i>Fermé
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $topic->posts_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ number_format($topic->views_count) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $topic->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('forum.topics.show', [$topic->category->slug ?? 'general', $topic->slug]) }}" 
                                           target="_blank"
                                           class="text-gray-400 hover:text-indigo-600"
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <button onclick="togglePin({{ $topic->id }})" 
                                                class="text-gray-400 hover:text-indigo-600"
                                                title="{{ $topic->is_sticky ? 'Désépingler' : 'Épingler' }}">
                                            <i class="fas fa-thumbtack {{ $topic->is_sticky ? 'text-indigo-600' : '' }}"></i>
                                        </button>
                                        
                                        <button onclick="toggleClose({{ $topic->id }})" 
                                                class="text-gray-400 hover:text-yellow-600"
                                                title="{{ $topic->status === 'closed' ? 'Rouvrir' : 'Fermer' }}">
                                            <i class="fas fa-{{ $topic->status === 'closed' ? 'lock-open' : 'lock' }}"></i>
                                        </button>
                                        
                                        <form action="{{ route('admin.forum.topics.destroy', $topic) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer définitivement ce sujet ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-comments text-4xl mb-3 opacity-30"></i>
                                    <p>Aucun sujet trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Actions groupées -->
            @if($topics->count() > 0)
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center">
                    <select id="bulk-action" class="text-sm border-gray-300 rounded-lg mr-3">
                        <option value="">Actions groupées</option>
                        <option value="pin">Épingler</option>
                        <option value="unpin">Désépingler</option>
                        <option value="close">Fermer</option>
                        <option value="open">Ouvrir</option>
                        <option value="delete">Supprimer</option>
                    </select>
                    <button onclick="applyBulkAction()" class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                        Appliquer
                    </button>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $topics->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePin(topicId) {
        fetch(`/admin/forum/topics/${topicId}/pin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.reload();
              }
          });
    }
    
    function toggleClose(topicId) {
        fetch(`/admin/forum/topics/${topicId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.reload();
              }
          });
    }
    
    function applyBulkAction() {
        const action = document.getElementById('bulk-action').value;
        if (!action) {
            alert('Veuillez sélectionner une action');
            return;
        }
        
        const selected = [];
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
            if (cb.value) selected.push(cb.value);
        });
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un sujet');
            return;
        }
        
        if (action === 'delete' && !confirm('Supprimer les sujets sélectionnés ?')) {
            return;
        }
        
        fetch('/admin/forum/topics/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ action, ids: selected })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.reload();
              }
          });
    }
</script>
@endpush