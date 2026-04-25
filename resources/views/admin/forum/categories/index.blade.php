@extends('layouts.admin')

@section('title', 'Gestion du forum')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Forum</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="forumAdminManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion du forum</h1>
                <p class="text-gray-500 mt-1">Gérez les catégories, sujets et messages du forum</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.forum.statistics') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </a>
                <button @click="openCreateCategoryModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Nouvelle catégorie
                </button>
            </div>
        </div>

        <!-- Onglets -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('admin.forum.categories.index') }}" 
                   class="border-indigo-600 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-folder mr-2"></i>Catégories
                </a>
                <a href="{{ route('admin.forum.topics.index') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-comments mr-2"></i>Sujets
                </a>
                <a href="{{ route('admin.forum.posts.index') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-reply mr-2"></i>Messages
                </a>
            </nav>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Catégories</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $categories->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder text-indigo-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total sujets</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $categories->sum('topics_count') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Catégories actives</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $categories->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <!--  statistique "Dernière activité" -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Dernière activité</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            $lastActivity = \App\Models\ForumTopic::max('last_post_at');
                        @endphp
                        @if($lastActivity)
                            @if(is_string($lastActivity))
                                {{ \Carbon\Carbon::parse($lastActivity)->diffForHumans() }}
                            @else
                                {{ $lastActivity->diffForHumans() }}
                            @endif
                        @else
                            Aucune
                        @endif
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
            </div>
        </div>
        </div>

        <!-- Liste des catégories -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Catégories du forum</h2>
                <div class="relative">
                    <input type="text" 
                           placeholder="Rechercher une catégorie..." 
                           x-model="searchQuery"
                           class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernier sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordre</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $index => $category)
                            <tr class="table-row-hover animate-slide-in" style="animation-delay: {{ $index * 30 }}ms">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-{{ $category->color }}-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-{{ $category->icon }} text-{{ $category->color }}-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $category->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ $category->description }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $category->topics_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $category->topics_sum_posts_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
    @if($category->lastTopic)
        <a href="{{ $category->lastTopic->url ?? '#' }}" class="text-sm text-indigo-600 hover:text-indigo-700">
            {{ Str::limit($category->lastTopic->title, 30) }}
        </a>
        <p class="text-xs text-gray-500">
            @if($category->lastTopic->last_post_at)
                @if(is_string($category->lastTopic->last_post_at))
                    {{ \Carbon\Carbon::parse($category->lastTopic->last_post_at)->diffForHumans() }}
                @else
                    {{ $category->lastTopic->last_post_at->diffForHumans() }}
                @endif
            @else
                -
            @endif
        </p>
    @else
        <span class="text-sm text-gray-400">Aucun sujet</span>
    @endif
</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $category->order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.forum.categories.show', $category) }}" 
                                           class="text-gray-400 hover:text-indigo-600 transition-colors"
                                           target="_blank"
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button @click="editCategory({{ $category->id }})" 
                                                class="text-gray-400 hover:text-blue-600 transition-colors"
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="toggleStatus({{ $category->id }})" 
                                                class="text-gray-400 hover:text-yellow-600 transition-colors"
                                                title="{{ $category->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        <button @click="confirmDelete({{ $category->id }})" 
                                                class="text-gray-400 hover:text-red-600 transition-colors"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" 
                                                 @click.away="open = false"
                                                 x-transition
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                                <button @click="moveUp({{ $category->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-arrow-up mr-2"></i>Monter
                                                </button>
                                                <button @click="moveDown({{ $category->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-arrow-down mr-2"></i>Déplacer vers le bas
                                                </button>
                                                <hr class="my-1">
                                                <button @click="duplicate({{ $category->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-copy mr-2"></i>Dupliquer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-folder-open text-5xl mb-4 opacity-30"></i>
                                    <p class="text-lg font-medium">Aucune catégorie</p>
                                    <p class="text-sm mt-1">Commencez par créer votre première catégorie</p>
                                    <button @click="openCreateCategoryModal()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i class="fas fa-plus mr-2"></i>Nouvelle catégorie
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Actions groupées -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <select class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Actions groupées</option>
                        <option>Activer</option>
                        <option>Désactiver</option>
                        <option>Supprimer</option>
                    </select>
                    <button class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                        Appliquer
                    </button>
                </div>
                <p class="text-sm text-gray-500">
                    {{ $categories->count() }} catégorie(s) au total
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Création/Édition de catégorie -->
    <div x-show="categoryModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="categoryModalOpen = false"></div>
            
            <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h3>
                    <button @click="categoryModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form @submit.prevent="saveCategory">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la catégorie</label>
                                <input type="text" x-model="categoryForm.name" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Ex: Développement Web">
                            </div>
                            
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea x-model="categoryForm.description" rows="3"
                                          class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Description de la catégorie..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Icône</label>
                                <select x-model="categoryForm.icon" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="comments">💬 Comments</option>
                                    <option value="question-circle">❓ Question</option>
                                    <option value="bullhorn">📢 Bullhorn</option>
                                    <option value="link">🔗 Link</option>
                                    <option value="lightbulb">💡 Lightbulb</option>
                                    <option value="user-plus">👤 User Plus</option>
                                    <option value="folder">📁 Folder</option>
                                    <option value="code">💻 Code</option>
                                    <option value="book">📚 Book</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                                <select x-model="categoryForm.color" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="indigo">Indigo</option>
                                    <option value="blue">Bleu</option>
                                    <option value="green">Vert</option>
                                    <option value="red">Rouge</option>
                                    <option value="yellow">Jaune</option>
                                    <option value="purple">Violet</option>
                                    <option value="pink">Rose</option>
                                    <option value="orange">Orange</option>
                                    <option value="teal">Turquoise</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ordre</label>
                                <input type="number" x-model="categoryForm.order" min="0"
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="categoryForm.is_active"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Catégorie active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="categoryModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <span x-text="editingCategory ? 'Mettre à jour' : 'Créer'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p class="text-gray-500 mb-6">
                        Êtes-vous sûr de vouloir supprimer cette catégorie ? 
                        <br><span class="text-red-600 font-medium">Cette action est irréversible.</span>
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button @click="deleteCategory()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function forumAdminManager() {
        return {
            searchQuery: '',
            categoryModalOpen: false,
            deleteModalOpen: false,
            modalTitle: 'Nouvelle catégorie',
            editingCategory: null,
            categoryToDelete: null,
            categoryForm: {
                name: '',
                description: '',
                icon: 'comments',
                color: 'indigo',
                order: 0,
                is_active: true
            },
            
            openCreateCategoryModal() {
                this.editingCategory = null;
                this.modalTitle = 'Nouvelle catégorie';
                this.categoryForm = {
                    name: '',
                    description: '',
                    icon: 'comments',
                    color: 'indigo',
                    order: 0,
                    is_active: true
                };
                this.categoryModalOpen = true;
            },
            
            editCategory(id) {
                this.editingCategory = id;
                this.modalTitle = 'Modifier la catégorie';
                // Charger les données via API
                fetch(`/api/admin/forum/categories/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        this.categoryForm = {
                            name: data.name,
                            description: data.description,
                            icon: data.icon,
                            color: data.color,
                            order: data.order,
                            is_active: data.is_active
                        };
                    });
                this.categoryModalOpen = true;
            },
            
            saveCategory() {
                const url = this.editingCategory 
                    ? `/api/admin/forum/categories/${this.editingCategory}`
                    : '/api/admin/forum/categories';
                const method = this.editingCategory ? 'PUT' : 'POST';
                
                fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.categoryForm)
                })
                .then(r => r.json())
                .then(data => {
                    this.categoryModalOpen = false;
                    window.location.reload();
                });
            },
            
            toggleStatus(id) {
                fetch(`/api/admin/forum/categories/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => window.location.reload());
            },
            
            confirmDelete(id) {
                this.categoryToDelete = id;
                this.deleteModalOpen = true;
            },
            
            deleteCategory() {
                fetch(`/api/admin/forum/categories/${this.categoryToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => window.location.reload());
            },
            
            moveUp(id) {
                fetch(`/api/admin/forum/categories/${id}/move-up`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => window.location.reload());
            },
            
            moveDown(id) {
                fetch(`/api/admin/forum/categories/${id}/move-down`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => window.location.reload());
            },
            
            duplicate(id) {
                fetch(`/api/admin/forum/categories/${id}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => window.location.reload());
            }
        }
    }
</script>
@endpush