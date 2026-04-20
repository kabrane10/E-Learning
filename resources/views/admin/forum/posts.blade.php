@extends('layouts.admin')

@section('title', 'Messages du forum')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Messages du forum</h1>
                <p class="text-gray-500 mt-1">Gérez tous les messages et réponses</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.forum.topics.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-comments mr-2"></i>Sujets
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contenu du message..."
                           class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Auteur</label>
                    <select name="user_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sujet</label>
                    <select name="topic_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        @foreach($topicsList ?? [] as $topicItem)
                            <option value="{{ $topicItem->id }}" {{ request('topic_id') == $topicItem->id ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::limit($topicItem->title, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Solution</label>
                    <select name="is_solution" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        <option value="1" {{ request('is_solution') == '1' ? 'selected' : '' }}>Solutions</option>
                        <option value="0" {{ request('is_solution') == '0' ? 'selected' : '' }}>Non-solutions</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.forum.posts.index') }}" class="ml-2 px-4 py-2 text-gray-600 hover:text-gray-900">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des messages -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <input type="checkbox" class="rounded border-gray-300" id="select-all">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                            <th class="px-6 py-3 text15 text-xs font-medium text-gray-500 uppercase">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Likes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solution</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($posts as $post)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-gray-300 post-checkbox" value="{{ $post->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) }}" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $post->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $post->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <p class="text-sm text-gray-700 line-clamp-2">
                                            {!! strip_tags($post->content) !!}
                                        </p>
                                        @if($post->is_edited)
                                            <span class="text-xs text-gray-400">
                                                <i class="fas fa-pencil-alt mr-1"></i>Modifié
                                            </span>
                                        @endif
                                        @if($post->parent_id)
                                            <span class="text-xs text-indigo-500 ml-2">
                                                <i class="fas fa-reply mr-1"></i>Réponse
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($post->topic)
                                        <a href="{{ route('forum.topics.show', [$post->topic->category->slug ?? 'general', $post->topic->slug]) }}#post-{{ $post->id }}" 
                                           target="_blank"
                                           class="text-sm text-indigo-600 hover:text-indigo-700 line-clamp-1">
                                            {{ $post->topic->title }}
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-400">Sujet supprimé</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <i class="far fa-heart text-red-500 mr-1"></i>
                                    {{ $post->likes_count }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($post->is_solution)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>Solution
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $post->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        @if($post->topic)
                                            <a href="{{ route('forum.topics.show', [$post->topic->category->slug ?? 'general', $post->topic->slug]) }}#post-{{ $post->id }}" 
                                               target="_blank"
                                               class="text-gray-400 hover:text-indigo-600"
                                               title="Voir dans le contexte">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        
                                        @if($post->topic && !$post->is_solution && $post->topic->type === 'question')
                                            <button onclick="markAsSolution({{ $post->id }})" 
                                                    class="text-gray-400 hover:text-green-600"
                                                    title="Marquer comme solution">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        <form action="{{ route('admin.forum.posts.destroy', $post) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer définitivement ce message ?')">
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
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-envelope text-4xl mb-3 opacity-30"></i>
                                    <p>Aucun message trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Actions groupées -->
            @if($posts->count() > 0)
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center">
                    <select id="bulk-action" class="text-sm border-gray-300 rounded-lg mr-3">
                        <option value="">Actions groupées</option>
                        <option value="delete">Supprimer</option>
                    </select>
                    <button onclick="applyBulkAction()" class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                        Appliquer
                    </button>
                    <span class="ml-4 text-sm text-gray-500">
                        {{ $posts->total() }} message(s) au total
                    </span>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $posts->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sélectionner/désélectionner tous
    document.getElementById('select-all')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.post-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    
    function markAsSolution(postId) {
        if (!confirm('Marquer ce message comme solution ?')) return;
        
        fetch(`/admin/forum/posts/${postId}/solution`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.reload();
              } else {
                  alert('Erreur lors du marquage de la solution');
              }
          })
          .catch(error => {
              console.error('Erreur:', error);
              alert('Une erreur est survenue');
          });
    }
    
    function applyBulkAction() {
        const action = document.getElementById('bulk-action').value;
        if (!action) {
            alert('Veuillez sélectionner une action');
            return;
        }
        
        const selected = [];
        document.querySelectorAll('.post-checkbox:checked').forEach(cb => {
            if (cb.value) selected.push(cb.value);
        });
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un message');
            return;
        }
        
        if (action === 'delete' && !confirm('Supprimer les ' + selected.length + ' messages sélectionnés ?')) {
            return;
        }
        
        fetch('/admin/forum/posts/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ action, ids: selected })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.reload();
              } else {
                  alert(data.message || 'Erreur lors de l\'action groupée');
              }
          })
          .catch(error => {
              console.error('Erreur:', error);
              alert('Une erreur est survenue');
          });
    }
</script>
@endpush