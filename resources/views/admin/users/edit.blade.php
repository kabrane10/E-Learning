@extends('layouts.admin')

@section('title', 'Modifier - ' . $user->name)

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-500">Utilisateurs</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.users.show', $user) }}" class="text-gray-400 hover:text-gray-500">{{ $user->name }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Modifier</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="userEditForm({{ $user->id }})">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier l'utilisateur</h1>
                <p class="text-gray-500 mt-1">{{ $user->name }} • {{ $user->email }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.show', $user) }}" 
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Annuler
                </a>
                <button type="submit" form="user-edit-form"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </div>
        
        <form id="user-edit-form" action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Informations de base -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-user-circle text-indigo-600 mr-2"></i>
                            Informations de base
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Avatar -->
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <img id="avatar-preview" 
                                     src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=96' }}" 
                                     class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                                <div class="flex items-center space-x-3">
                                    <label class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors">
                                        <i class="fas fa-upload mr-2"></i>Changer l'avatar
                                        <input type="file" name="avatar" accept="image/*" class="hidden" @change="previewAvatar">
                                    </label>
                                    <button type="button" @click="removeAvatar" class="text-sm text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash mr-1"></i>Supprimer
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG ou GIF • Max 2MB</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('name') border-red-300 @enderror"
                                           placeholder="John Doe">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adresse email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('email') border-red-300 @enderror"
                                           placeholder="john@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rôle <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <select name="role" 
                                            id="role" 
                                            required
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('role') border-red-300 @enderror">
                                        <option value="">Sélectionner un rôle</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-circle"></i>
                                    </span>
                                    <select name="status" 
                                            id="status"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                        <option value="banned" {{ $user->status === 'banned' ? 'selected' : '' }}>Banni</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="email_verified" 
                                       value="1"
                                       {{ $user->email_verified_at ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Email vérifié</span>
                            </label>
                            @if(!$user->email_verified_at)
                                <p class="text-xs text-yellow-600 mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    L'email n'a pas encore été vérifié
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Sécurité -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-shield-alt text-indigo-600 mr-2"></i>
                            Sécurité
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nouveau mot de passe
                                </label>
                                <div class="relative" x-data="{ showPassword: false }">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           id="password"
                                           class="w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('password') border-red-300 @enderror"
                                           placeholder="Laisser vide pour ne pas changer">
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    Minimum 8 caractères. Laissez vide pour conserver le mot de passe actuel.
                                </p>
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmer le mot de passe
                                </label>
                                <div class="relative" x-data="{ showConfirm: false }">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input :type="showConfirm ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           class="w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                           placeholder="Confirmer le nouveau mot de passe">
                                    <button type="button" 
                                            @click="showConfirm = !showConfirm"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Authentification à deux facteurs</h3>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">2FA</p>
                                    <p class="text-xs text-gray-500">
                                        @if($user->two_factor_confirmed_at)
                                            Activée le {{ $user->two_factor_confirmed_at->format('d/m/Y') }}
                                        @else
                                            Non configurée
                                        @endif
                                    </p>
                                </div>
                                <button type="button" 
                                        @click="toggle2FA"
                                        class="text-sm {{ $user->two_factor_confirmed_at ? 'text-red-600 hover:text-red-700' : 'text-indigo-600 hover:text-indigo-700' }}">
                                    {{ $user->two_factor_confirmed_at ? 'Désactiver' : 'Activer' }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Sessions actives</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-desktop text-gray-400"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Session actuelle</p>
                                            <p class="text-xs text-gray-500">{{ request()->ip() }} • {{ request()->userAgent() }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Actif</span>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="logoutAllSessions"
                                    class="mt-3 text-sm text-red-600 hover:text-red-700">
                                <i class="fas fa-sign-out-alt mr-1"></i>Déconnecter toutes les sessions
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Préférences -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-sliders-h text-indigo-600 mr-2"></i>
                            Préférences
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                Langue
                            </label>
                            <select name="language" id="language"
                                    class="w-full md:w-64 rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="fr" {{ ($user->preferences['language'] ?? 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ ($user->preferences['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ ($user->preferences['language'] ?? '') === 'es' ? 'selected' : '' }}>Español</option>
                                <option value="de" {{ ($user->preferences['language'] ?? '') === 'de' ? 'selected' : '' }}>Deutsch</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                Fuseau horaire
                            </label>
                            <select name="timezone" id="timezone"
                                    class="w-full md:w-64 rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Europe/Paris" {{ ($user->preferences['timezone'] ?? 'Europe/Paris') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                <option value="Europe/London" {{ ($user->preferences['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                <option value="America/New_York" {{ ($user->preferences['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Asia/Tokyo" {{ ($user->preferences['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
                            </select>
                        </div>
                        
                        <div class="space-y-3 pt-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notifications_email" 
                                       value="1"
                                       {{ ($user->preferences['notifications_email'] ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Recevoir les notifications par email</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notifications_push" 
                                       value="1"
                                       {{ ($user->preferences['notifications_push'] ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Recevoir les notifications push</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="newsletter" 
                                       value="1"
                                       {{ ($user->preferences['newsletter'] ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Recevoir la newsletter</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Notes internes (Admin uniquement) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-sticky-note text-indigo-600 mr-2"></i>
                            Notes internes
                        </h2>
                    </div>
                    <div class="p-6">
                        <textarea name="admin_notes" 
                                  rows="3"
                                  class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Notes visibles uniquement par les administrateurs...">{{ old('admin_notes', $user->admin_notes ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-lock mr-1"></i>Ces notes sont privées et visibles uniquement par les administrateurs.
                        </p>
                    </div>
                </div>
                
                <!-- Actions rapides -->
                <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-6">
                    <h3 class="text-sm font-medium text-yellow-800 mb-3 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Actions rapides
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" 
                                @click="sendPasswordReset"
                                class="px-4 py-2 bg-white border border-yellow-300 text-yellow-800 rounded-lg hover:bg-yellow-100 transition-colors text-sm">
                            <i class="fas fa-key mr-2"></i>Envoyer réinitialisation du mot de passe
                        </button>
                        <button type="button" 
                                @click="sendVerificationEmail"
                                class="px-4 py-2 bg-white border border-yellow-300 text-yellow-800 rounded-lg hover:bg-yellow-100 transition-colors text-sm">
                            <i class="fas fa-envelope mr-2"></i>Renvoyer l'email de vérification
                        </button>
                        <button type="button" 
                                @click="impersonate"
                                class="px-4 py-2 bg-white border border-yellow-300 text-yellow-800 rounded-lg hover:bg-yellow-100 transition-colors text-sm">
                            <i class="fas fa-user-secret mr-2"></i>Usurper l'identité
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Modal de confirmation -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="modalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="modalTitle"></h3>
                    <p class="text-gray-500 mb-6" x-text="modalMessage"></p>
                    <button @click="modalOpen = false"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function userEditForm(userId) {
        return {
            modalOpen: false,
            modalTitle: '',
            modalMessage: '',
            
            previewAvatar(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('avatar-preview').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            
            removeAvatar() {
                document.getElementById('avatar-preview').src = 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=96';
                // Ajouter un input hidden pour indiquer la suppression
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_avatar';
                input.value = '1';
                document.getElementById('user-edit-form').appendChild(input);
            },
            
            toggle2FA() {
                if (confirm('{{ $user->two_factor_confirmed_at ? "Désactiver" : "Activer" }} l\'authentification à deux facteurs ?')) {
                    // Logique 2FA
                    this.showModal('2FA', 'L\'authentification à deux facteurs a été {{ $user->two_factor_confirmed_at ? "désactivée" : "activée" }}.');
                }
            },
            
            logoutAllSessions() {
                if (confirm('Déconnecter toutes les sessions actives de cet utilisateur ?')) {
                    fetch(`/admin/users/${userId}/logout-all`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(() => {
                        this.showModal('Sessions', 'Toutes les sessions ont été déconnectées.');
                    });
                }
            },
            
            sendPasswordReset() {
                if (confirm('Envoyer un email de réinitialisation de mot de passe à {{ $user->email }} ?')) {
                    fetch(`/admin/users/${userId}/send-password-reset`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(() => {
                        this.showModal('Email envoyé', 'L\'email de réinitialisation a été envoyé avec succès.');
                    });
                }
            },
            
            sendVerificationEmail() {
                if (confirm('Renvoyer l\'email de vérification à {{ $user->email }} ?')) {
                    fetch(`/admin/users/${userId}/send-verification`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(() => {
                        this.showModal('Email envoyé', 'L\'email de vérification a été renvoyé avec succès.');
                    });
                }
            },
            
            impersonate() {
                if (confirm('Usurper l\'identité de {{ $user->name }} ?')) {
                    window.location.href = '{{ route("admin.users.impersonate", $user) }}';
                }
            },
            
            showModal(title, message) {
                this.modalTitle = title;
                this.modalMessage = message;
                this.modalOpen = true;
            }
        }
    }
</script>
@endpush