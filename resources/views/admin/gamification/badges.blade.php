@extends('layouts.admin')

@section('title', 'Gestion des badges')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des badges</h1>
                <p class="text-gray-500 mt-1">Créez et gérez les badges de la plateforme</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.gamification.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Nouveau badge
                </button>
            </div>
        </div>

        <!-- Filtres par catégorie -->
        <div class="mb-6 flex flex-wrap gap-2">
            <a href="{{ route('admin.gamification.badges') }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Tous
            </a>
            <a href="{{ route('admin.gamification.badges', ['category' => 'course']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('category') == 'course' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Cours
            </a>
            <a href="{{ route('admin.gamification.badges', ['category' => 'quiz']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('category') == 'quiz' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Quiz
            </a>
            <a href="{{ route('admin.gamification.badges', ['category' => 'activity']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('category') == 'activity' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Activité
            </a>
            <a href="{{ route('admin.gamification.badges', ['category' => 'special']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('category') == 'special' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Spécial
            </a>
        </div>

        <!-- Grille des badges -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($badges as $badge)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-14 bg-{{ $badge->color }}-100 rounded-xl flex items-center justify-center">
                                    <span class="text-3xl">{{ $badge->icon }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $badge->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $badge->slug }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1">
                                @if($badge->is_secret)
                                    <span class="text-purple-600" title="Badge secret">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                @endif
                                <span class="px-2 py-1 text-xs rounded-full {{ $badge->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $badge->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">{{ $badge->description }}</p>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Catégorie:</span>
                                <span class="font-medium text-gray-900">{{ ucfirst($badge->category) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Points:</span>
                                <span class="font-medium text-green-600">+{{ $badge->points_reward }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Critère:</span>
                                <span class="font-medium text-gray-900">
                                    @php
                                        // Gérer le cas où criteria est déjà un tableau (casté par le modèle)
                                        $criteria = is_array($badge->criteria) ? $badge->criteria : json_decode($badge->criteria, true);
                                    @endphp
                                    {{ $criteria['type'] ?? 'N/A' }} ({{ $criteria['count'] ?? 0 }})
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Obtenu par:</span>
                                <span class="font-medium text-gray-900">{{ $badge->users_count ?? 0 }} utilisateurs</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                            <span class="text-xs text-gray-400">
                                <i class="far fa-calendar mr-1"></i>{{ $badge->created_at->format('d/m/Y') }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <button onclick="toggleActive({{ $badge->id }})" 
                                        class="text-sm {{ $badge->is_active ? 'text-yellow-600 hover:text-yellow-700' : 'text-green-600 hover:text-green-700' }}">
                                    <i class="fas fa-{{ $badge->is_active ? 'ban' : 'check-circle' }}"></i>
                                </button>
                                <button onclick="editBadge({{ $badge->id }})" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.gamification.badges.destroy', $badge) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Supprimer ce badge ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-12 text-center text-gray-500">
                    <i class="fas fa-medal text-5xl mb-4 opacity-30"></i>
                    <p class="text-lg">Aucun badge trouvé</p>
                    <button onclick="openCreateModal()" class="mt-4 text-indigo-600 hover:text-indigo-700">
                        <i class="fas fa-plus mr-1"></i>Créer le premier badge
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Création/Édition -->
<div id="badgeModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Nouveau badge</h3>
            </div>
            
            <form id="badgeForm" method="POST" action="{{ route('admin.gamification.badges.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="badgeName" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="slug" id="badgeSlug" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="badgeDescription" rows="2" required
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icône</label>
                            <select name="icon" id="badgeIcon" class="w-full rounded-lg border-gray-300">
                                <option value="🎓">🎓 Diplôme</option>
                                <option value="📖">📖 Livre</option>
                                <option value="🏆">🏆 Trophée</option>
                                <option value="❓">❓ Question</option>
                                <option value="💯">💯 100%</option>
                                <option value="🔥">🔥 Feu</option>
                                <option value="⚡">⚡ Éclair</option>
                                <option value="✍️">✍️ Plume</option>
                                <option value="📝">📝 Document</option>
                                <option value="⭐">⭐ Étoile</option>
                                <option value="🎯">🎯 Cible</option>
                                <option value="💪">💪 Force</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                            <select name="color" id="badgeColor" class="w-full rounded-lg border-gray-300">
                                <option value="green">Vert</option>
                                <option value="blue">Bleu</option>
                                <option value="gold">Or</option>
                                <option value="purple">Violet</option>
                                <option value="orange">Orange</option>
                                <option value="red">Rouge</option>
                                <option value="pink">Rose</option>
                                <option value="indigo">Indigo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                            <select name="category" id="badgeCategory" class="w-full rounded-lg border-gray-300">
                                <option value="course">Cours</option>
                                <option value="quiz">Quiz</option>
                                <option value="activity">Activité</option>
                                <option value="special">Spécial</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                            <input type="number" name="points_reward" id="badgePoints" value="100" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ordre</label>
                            <input type="number" name="order" id="badgeOrder" value="0" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de critère</label>
                            <select name="criteria_type" id="criteriaType" class="w-full rounded-lg border-gray-300">
                                <option value="courses_completed">Cours complétés</option>
                                <option value="quizzes_passed">Quiz réussis</option>
                                <option value="streak_days">Jours de série</option>
                                <option value="points_earned">Points gagnés</option>
                                <option value="reviews_written">Avis écrits</option>
                                <option value="perfect_quizzes">Quiz parfaits</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valeur</label>
                            <input type="number" name="criteria_count" id="criteriaCount" value="1" min="1"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_secret" id="badgeSecret" value="1"
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Badge secret</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="badgeActive" value="1" checked
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Actif</span>
                        </label>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('badgeModal');
    const form = document.getElementById('badgeForm');
    
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Nouveau badge';
        form.action = '{{ route("admin.gamification.badges.store") }}';
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        document.getElementById('badgeActive').checked = true;
        modal.classList.remove('hidden');
    }
    
    function editBadge(id) {
        fetch(`/admin/gamification/badges/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Modifier le badge';
                form.action = `/admin/gamification/badges/${id}`;
                document.getElementById('formMethod').value = 'PUT';
                
                document.getElementById('badgeName').value = data.name;
                document.getElementById('badgeSlug').value = data.slug;
                document.getElementById('badgeDescription').value = data.description;
                document.getElementById('badgeIcon').value = data.icon;
                document.getElementById('badgeColor').value = data.color;
                document.getElementById('badgeCategory').value = data.category;
                document.getElementById('badgePoints').value = data.points_reward;
                document.getElementById('badgeOrder').value = data.order;
                
                // Gérer le cas où criteria est déjà un tableau
                const criteria = typeof data.criteria === 'string' ? JSON.parse(data.criteria) : data.criteria;
                document.getElementById('criteriaType').value = criteria.type || 'courses_completed';
                document.getElementById('criteriaCount').value = criteria.count || 1;
                
                document.getElementById('badgeSecret').checked = data.is_secret || false;
                document.getElementById('badgeActive').checked = data.is_active || false;
                
                modal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement du badge');
            });
    }
    
    function toggleActive(id) {
        fetch(`/admin/gamification/badges/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(() => window.location.reload())
          .catch(error => console.error('Erreur:', error));
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    // Génération automatique du slug
    document.getElementById('badgeName')?.addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('badgeSlug').value = slug;
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>
@endpush