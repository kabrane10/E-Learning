@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Utilisateurs</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="userManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
                <p class="text-gray-500 mt-1">Gérez tous les utilisateurs de la plateforme</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button @click="openCreateModal()" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvel utilisateur
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Nom ou email..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rôle</label>
                    <select name="role" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Formateur</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Étudiant</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banni</option>
                    </select>
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscription</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users ?? [] as $index => $user)
                            <tr class="table-row-hover animate-slide-in" style="animation-delay: {{ $index * 30 }}ms">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $user->avatar }}" class="w-10 h-10 rounded-full mr-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                               {{ $user->hasRole('admin') ? 'bg-red-100 text-red-700' : 
                                                  ($user->hasRole('instructor') ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                        {{ ucfirst($user->roles->first()->name ?? 'student') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        <i class="fas fa-circle text-xs mr-1"></i>Actif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->enrolledCourses->count() ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="viewUser({{ $user->id }})" 
                                                class="text-gray-400 hover:text-indigo-600 transition-colors"
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button @click="editUser({{ $user->id }})" 
                                                class="text-gray-400 hover:text-blue-600 transition-colors"
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="confirmDelete({{ $user->id }})" 
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
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-envelope mr-2"></i>Envoyer un email
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-ban mr-2"></i>Bannir
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                    <i class="fas fa-user-slash mr-2"></i>Désactiver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-5xl mb-4 opacity-30"></i>
                                    <p class="text-lg font-medium">Aucun utilisateur trouvé</p>
                                    <p class="text-sm mt-1">Essayez d'ajuster vos filtres</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($users) && $users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
        
        <!-- Bulk Actions -->
        <div class="mt-4 flex items-center space-x-3">
            <select class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                <option>Actions groupées</option>
                <option>Supprimer</option>
                <option>Changer le rôle</option>
                <option>Envoyer un email</option>
            </select>
            <button class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                Appliquer
            </button>
            <span class="text-sm text-gray-500 ml-auto">
                {{ $users->total() ?? 0 }} utilisateurs au total
            </span>
        </div>
    </div>
    
    <!-- User Modal -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="modalOpen = false"></div>
            
            <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form @submit.prevent="saveUser">
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                                <input type="text" x-model="userForm.name" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" x-model="userForm.email" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                                <select x-model="userForm.role" required
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="student">Étudiant</option>
                                    <option value="instructor">Formateur</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                                <select x-model="userForm.status"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="active">Actif</option>
                                    <option value="inactive">Inactif</option>
                                    <option value="banned">Banni</option>
                                </select>
                            </div>
                        </div>
                        
                        <div x-show="!editingUser">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <input type="password" x-model="userForm.password"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour générer automatiquement</p>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <span x-text="editingUser ? 'Mettre à jour' : 'Créer'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
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
                    <p class="text-gray-500 mb-6">Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button @click="deleteUser()"
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
    function userManager() {
        return {
            modalOpen: false,
            deleteModalOpen: false,
            editingUser: null,
            userToDelete: null,
            modalTitle: 'Nouvel utilisateur',
            userForm: {
                name: '',
                email: '',
                role: 'student',
                status: 'active',
                password: ''
            },
            
            openCreateModal() {
                this.editingUser = null;
                this.modalTitle = 'Nouvel utilisateur';
                this.userForm = {
                    name: '',
                    email: '',
                    role: 'student',
                    status: 'active',
                    password: ''
                };
                this.modalOpen = true;
            },
            
            editUser(id) {
                this.editingUser = id;
                this.modalTitle = 'Modifier l\'utilisateur';
                // Charger les données de l'utilisateur
                this.modalOpen = true;
            },
            
            viewUser(id) {
                window.location.href = `/admin/users/${id}`;
            },
            
            confirmDelete(id) {
                this.userToDelete = id;
                this.deleteModalOpen = true;
            },
            
            async saveUser() {
                // Logique de sauvegarde
                this.modalOpen = false;
                // Afficher notification de succès
            },
            
            async deleteUser() {
                // Logique de suppression
                this.deleteModalOpen = false;
                // Afficher notification de succès
            }
        }
    }
</script>
@endpush