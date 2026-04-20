@extends('layouts.admin')

@section('title', 'Gestion des succès')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des succès</h1>
                <p class="text-gray-500 mt-1">Créez et gérez les succès (achievements)</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.gamification.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Nouveau succès
                </button>
            </div>
        </div>

        <!-- Grille des succès -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($achievements as $achievement)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center"
                                     style="background-color: {{ $achievement->tier_color ?? '#CD7F32' }}20">
                                    <span class="text-3xl">{{ $achievement->icon }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $achievement->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $achievement->slug }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="px-3 py-1 text-xs font-medium rounded-full text-white"
                                      style="background-color: {{ $achievement->tier_color ?? '#CD7F32' }}">
                                    {{ $achievement->tier_name ?? 'Bronze' }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full mt-1 {{ $achievement->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $achievement->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">{{ $achievement->description }}</p>
                        
                        <div class="space-y-2 text-sm bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Catégorie:</span>
                                <span class="font-medium text-gray-900">{{ ucfirst($achievement->category) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Points:</span>
                                <span class="font-medium text-green-600">+{{ $achievement->points_reward }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tier:</span>
                                <span class="font-medium text-gray-900">{{ $achievement->tier }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Complété par:</span>
                                <span class="font-medium text-gray-900">{{ $achievement->users_count ?? 0 }} utilisateurs</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Ordre:</span>
                                <span class="font-medium text-gray-900">{{ $achievement->order }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                            <span class="text-xs text-gray-400">
                                <i class="far fa-calendar mr-1"></i>{{ $achievement->created_at->format('d/m/Y') }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <button onclick="toggleActive({{ $achievement->id }})" 
                                        class="text-sm {{ $achievement->is_active ? 'text-yellow-600 hover:text-yellow-700' : 'text-green-600 hover:text-green-700' }}">
                                    <i class="fas fa-{{ $achievement->is_active ? 'ban' : 'check-circle' }}"></i>
                                </button>
                                <button onclick="editAchievement({{ $achievement->id }})" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.gamification.achievements.destroy', $achievement) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Supprimer ce succès ?')">
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
                <div class="col-span-2 py-12 text-center text-gray-500">
                    <i class="fas fa-trophy text-5xl mb-4 opacity-30"></i>
                    <p class="text-lg">Aucun succès trouvé</p>
                    <button onclick="openCreateModal()" class="mt-4 text-indigo-600 hover:text-indigo-700">
                        <i class="fas fa-plus mr-1"></i>Créer le premier succès
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Création/Édition -->
<div id="achievementModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Nouveau succès</h3>
            </div>
            
            <form id="achievementForm" method="POST" action="{{ route('admin.gamification.achievements.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="achievementName" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="slug" id="achievementSlug" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="achievementDescription" rows="2" required
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icône</label>
                            <select name="icon" id="achievementIcon" class="w-full rounded-lg border-gray-300">
                                <option value="⏱️">⏱️ Chronomètre</option>
                                <option value="🍿">🍿 Popcorn</option>
                                <option value="🎬">🎬 Cinéma</option>
                                <option value="📊">📊 Graphique</option>
                                <option value="🏅">🏅 Médaille</option>
                                <option value="🔥">🔥 Feu</option>
                                <option value="🦅">🦅 Aigle</option>
                                <option value="⭐">⭐ Étoile</option>
                                <option value="💎">💎 Diamant</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                            <select name="category" id="achievementCategory" class="w-full rounded-lg border-gray-300">
                                <option value="learning">Apprentissage</option>
                                <option value="teaching">Enseignement</option>
                                <option value="community">Communauté</option>
                                <option value="special">Spécial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tier</label>
                            <select name="tier" id="achievementTier" class="w-full rounded-lg border-gray-300">
                                <option value="1">1 - Bronze</option>
                                <option value="2">2 - Argent</option>
                                <option value="3">3 - Or</option>
                                <option value="4">4 - Platine</option>
                                <option value="5">5 - Diamant</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                            <input type="number" name="points_reward" id="achievementPoints" value="300" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ordre</label>
                            <input type="number" name="order" id="achievementOrder" value="0" min="0"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de condition</label>
                            <select name="requirements_type" id="requirementsType" class="w-full rounded-lg border-gray-300">
                                <option value="watch_time">Temps de visionnage (minutes)</option>
                                <option value="courses_completed">Cours complétés</option>
                                <option value="quizzes_passed">Quiz réussis</option>
                                <option value="streak_days">Jours de série</option>
                                <option value="points_earned">Points gagnés</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valeur</label>
                            <input type="number" name="requirements_value" id="requirementsValue" value="600" min="1"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="achievementActive" value="1" checked
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
    const modal = document.getElementById('achievementModal');
    const form = document.getElementById('achievementForm');
    
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Nouveau succès';
        form.action = '{{ route("admin.gamification.achievements.store") }}';
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        modal.classList.remove('hidden');
    }
    
    function editAchievement(id) {
        fetch(`/admin/gamification/achievements/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Modifier le succès';
                form.action = `/admin/gamification/achievements/${id}`;
                document.getElementById('formMethod').value = 'PUT';
                
                document.getElementById('achievementName').value = data.name;
                document.getElementById('achievementSlug').value = data.slug;
                document.getElementById('achievementDescription').value = data.description;
                document.getElementById('achievementIcon').value = data.icon;
                document.getElementById('achievementCategory').value = data.category;
                document.getElementById('achievementTier').value = data.tier;
                document.getElementById('achievementPoints').value = data.points_reward;
                document.getElementById('achievementOrder').value = data.order;
                
                const requirements = JSON.parse(data.requirements);
                document.getElementById('requirementsType').value = requirements.type;
                document.getElementById('requirementsValue').value = requirements[requirements.type === 'watch_time' ? 'minutes' : (requirements.type === 'streak_days' ? 'days' : (requirements.type === 'points_earned' ? 'points' : 'count'))] || 1;
                
                document.getElementById('achievementActive').checked = data.is_active;
                
                modal.classList.remove('hidden');
            });
    }
    
    function toggleActive(id) {
        fetch(`/admin/gamification/achievements/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => window.location.reload());
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    document.getElementById('achievementName')?.addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-');
        document.getElementById('achievementSlug').value = slug;
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>
@endpush