@extends('layouts.admin')

@section('title', 'Gestion des niveaux')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des niveaux</h1>
                <p class="text-gray-500 mt-1">Configurez les niveaux et les points requis</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.gamification.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Nouveau niveau
                </button>
            </div>
        </div>

        <!-- Liste des niveaux -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icône</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Couleur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Points requis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Récompenses</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($levels as $level)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                        Niveau {{ $level->level_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $level->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-2xl">{{ $level->icon }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm text-white" style="background-color: {{ $level->color === 'indigo' ? '#4f46e5' : ($level->color === 'green' ? '#10b981' : ($level->color === 'blue' ? '#3b82f6' : ($level->color === 'yellow' ? '#eab308' : ($level->color === 'orange' ? '#f97316' : ($level->color === 'purple' ? '#8b5cf6' : ($level->color === 'red' ? '#ef4444' : '#6b7280')))))) }}">
                                        {{ ucfirst($level->color) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-900">{{ number_format($level->points_required) }} XP</td>
                                <td class="px-6 py-4">
                                    @if($level->rewards)
                                        @php $rewards = json_decode($level->rewards, true); @endphp
                                        <div class="text-sm text-gray-600">
                                            @if(isset($rewards['points_bonus']))
                                                <span class="block">+{{ $rewards['points_bonus'] }} points bonus</span>
                                            @endif
                                            @if(isset($rewards['badge_unlock']))
                                                <span class="block">🎖️ Badge débloqué</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="editLevel({{ $level->id }})" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.gamification.levels.destroy', $level) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer ce niveau ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-chart-line text-4xl mb-3 opacity-30"></i>
                                    <p>Aucun niveau configuré</p>
                                    <button onclick="openCreateModal()" class="mt-4 text-indigo-600 hover:text-indigo-700">
                                        <i class="fas fa-plus mr-1"></i>Créer le premier niveau
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Visualisation de la progression -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Visualisation de la progression</h3>
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        @foreach($levels as $index => $level)
                            <span class="text-xs font-semibold inline-block text-{{ $level->color }}-600 absolute"
                                  style="left: {{ ($level->points_required / ($levels->max('points_required') ?: 1)) * 100 }}%; transform: translateX(-50%);">
                                {{ $level->icon }} {{ $level->level_number }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="overflow-hidden h-4 text-xs flex rounded bg-gray-200">
                    @php
                        $maxPoints = $levels->max('points_required') ?: 1;
                        $previousPoints = 0;
                    @endphp
                    @foreach($levels as $level)
                        @php
                            $width = (($level->points_required - $previousPoints) / $maxPoints) * 100;
                            $previousPoints = $level->points_required;
                        @endphp
                        <div style="width: {{ $width }}%; background-color: {{ $level->color === 'indigo' ? '#4f46e5' : ($level->color === 'green' ? '#10b981' : ($level->color === 'blue' ? '#3b82f6' : '#6b7280')) }}"
                             class="h-full border-r border-white last:border-0"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création/Édition de niveau -->
<div id="levelModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Nouveau niveau</h3>
            </div>
            
            <form id="levelForm" method="POST" action="{{ route('admin.gamification.levels.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="levelId" name="level_id">
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de niveau <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="level_number" id="levelNumber" required min="1"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="levelName" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ex: Débutant">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icône</label>
                            <select name="icon" id="levelIcon" class="w-full rounded-lg border-gray-300">
                                <option value="🌱">🌱 Débutant</option>
                                <option value="📚">📚 Apprenti</option>
                                <option value="🔰">🔰 Initié</option>
                                <option value="⚡">⚡ Confirmé</option>
                                <option value="🎯">🎯 Expert</option>
                                <option value="👑">👑 Maître</option>
                                <option value="🌟">🌟 Grand Maître</option>
                                <option value="💎">💎 Légende</option>
                                <option value="🔮">🔮 Élu</option>
                                <option value="∞">∞ Immortel</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                            <select name="color" id="levelColor" class="w-full rounded-lg border-gray-300">
                                <option value="green">Vert</option>
                                <option value="blue">Bleu</option>
                                <option value="cyan">Cyan</option>
                                <option value="yellow">Jaune</option>
                                <option value="orange">Orange</option>
                                <option value="purple">Violet</option>
                                <option value="pink">Rose</option>
                                <option value="indigo">Indigo</option>
                                <option value="red">Rouge</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Points requis <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="points_required" id="pointsRequired" required min="0"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Nombre de points nécessaires pour atteindre ce niveau</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Récompenses (JSON)</label>
                        <textarea name="rewards" id="levelRewards" rows="3"
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                                  placeholder='{"points_bonus": 500, "badge_unlock": true}'></textarea>
                        <p class="text-xs text-gray-500 mt-1">Format JSON - laissez vide si aucune récompense</p>
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
    const modal = document.getElementById('levelModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('levelForm');
    const formMethod = document.getElementById('formMethod');
    const levelId = document.getElementById('levelId');
    const levelNumber = document.getElementById('levelNumber');
    const levelName = document.getElementById('levelName');
    const levelIcon = document.getElementById('levelIcon');
    const levelColor = document.getElementById('levelColor');
    const pointsRequired = document.getElementById('pointsRequired');
    const levelRewards = document.getElementById('levelRewards');
    
    function openCreateModal() {
        modalTitle.textContent = 'Nouveau niveau';
        form.action = '{{ route("admin.gamification.levels.store") }}';
        formMethod.value = 'POST';
        levelId.value = '';
        levelNumber.value = '';
        levelName.value = '';
        levelIcon.value = '🌱';
        levelColor.value = 'green';
        pointsRequired.value = '';
        levelRewards.value = '';
        modal.classList.remove('hidden');
    }
    
    function editLevel(id) {
        fetch(`/admin/gamification/levels/${id}`)
            .then(response => response.json())
            .then(data => {
                modalTitle.textContent = 'Modifier le niveau';
                form.action = `/admin/gamification/levels/${id}`;
                formMethod.value = 'PUT';
                levelId.value = data.id;
                levelNumber.value = data.level_number;
                levelName.value = data.name;
                levelIcon.value = data.icon;
                levelColor.value = data.color;
                pointsRequired.value = data.points_required;
                levelRewards.value = data.rewards ? JSON.stringify(data.rewards) : '';
                modal.classList.remove('hidden');
            });
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    // Fermer avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>
@endpush