@extends('layouts.guest')

@section('title', 'Inscription')
@section('auth_title', 'Commencez votre aventure')
@section('auth_description', 'Inscrivez-vous gratuitement et accédez à des centaines de cours de qualité.')

@section('content')
<div x-data="{ 
    step: 1,
    showPassword: false,
    showPasswordConfirmation: false,
    acceptTerms: false,
    role: 'student',
    
    nextStep() {
        // Validation de l'étape 1
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // Réinitialiser les erreurs
        document.querySelectorAll('.field-error').forEach(el => el.remove());
        document.querySelectorAll('.error-border').forEach(el => {
            el.classList.remove('error-border', 'border-red-300');
            el.classList.add('border-gray-300');
        });
        
        let hasError = false;
        
        if (!name || name.length < 2) {
            this.showFieldError('name', 'Le nom doit contenir au moins 2 caractères');
            hasError = true;
        }
        
        if (!email || !emailRegex.test(email)) {
            this.showFieldError('email', 'Veuillez entrer une adresse email valide');
            hasError = true;
        }
        
        if (!hasError) {
            this.step = 2;
        }
    },
    
    showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        field.classList.add('error-border', 'border-red-300');
        field.classList.remove('border-gray-300');
        
        const errorDiv = document.createElement('p');
        errorDiv.className = 'field-error text-xs text-red-600 mt-1';
        errorDiv.textContent = message;
        field.parentElement.parentElement.appendChild(errorDiv);
    },
    
    prevStep() {
        this.step = 1;
    }
}" class="animate-fade-in">
    
    <!-- Titre -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Créer un compte</h1>
        <p class="text-gray-500 mt-1">
            Déjà un compte ? 
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                Se connecter
            </a>
        </p>
    </div>
    
    <!-- Indicateur d'étapes -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-all"
                     :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500'">
                    1
                </div>
                <span class="ml-3 text-sm font-medium" :class="step >= 1 ? 'text-gray-900' : 'text-gray-400'">
                    Informations
                </span>
            </div>
            <div class="flex-1 mx-4 h-0.5 bg-gray-200">
                <div class="h-full bg-indigo-600 transition-all duration-300" 
                     :style="'width: ' + (step === 2 ? '100%' : '0%')"></div>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-all"
                     :class="step === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500'">
                    2
                </div>
                <span class="ml-3 text-sm font-medium" :class="step === 2 ? 'text-gray-900' : 'text-gray-400'">
                    Sécurité
                </span>
            </div>
        </div>
    </div>
    
    <!-- Messages d'erreur serveur -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm text-red-700 font-medium mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>Veuillez corriger les erreurs suivantes :
            </p>
            <ul class="text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Formulaire -->
    <form method="POST" action="{{ route('register') }}" id="register-form" class="space-y-6">
        @csrf
        
        <!-- Champ caché pour le rôle -->
        <input type="hidden" name="role" :value="role">
        
        <!-- Étape 1 : Informations personnelles -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4">
            <!-- Type de compte -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Je suis un(e) <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Option Étudiant -->
                    <div class="relative">
                        <input type="radio" 
                               name="role_radio" 
                               id="role_student" 
                               value="student" 
                               x-model="role"
                               class="sr-only peer">
                        <label for="role_student" 
                               class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                      peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:border-gray-300">
                            <i class="fas fa-user-graduate text-2xl mr-3" :class="role === 'student' ? 'text-indigo-600' : 'text-gray-400'"></i>
                            <span class="font-medium" :class="role === 'student' ? 'text-indigo-600' : 'text-gray-700'">Étudiant</span>
                        </label>
                    </div>
                    
                    <!-- Option Formateur -->
                    <div class="relative">
                        <input type="radio" 
                               name="role_radio" 
                               id="role_instructor" 
                               value="instructor" 
                               x-model="role"
                               class="sr-only peer">
                        <label for="role_instructor" 
                               class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                      peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:border-gray-300">
                            <i class="fas fa-chalkboard-teacher text-2xl mr-3" :class="role === 'instructor' ? 'text-indigo-600' : 'text-gray-400'"></i>
                            <span class="font-medium" :class="role === 'instructor' ? 'text-indigo-600' : 'text-gray-700'">Formateur</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Nom complet -->
            <div class="mb-4">
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
                           value="{{ old('name') }}" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('name') border-red-300 @enderror"
                           placeholder="John Doe">
                </div>
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Email -->
            <div class="mb-4">
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
                           value="{{ old('email') }}" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('email') border-red-300 @enderror"
                           placeholder="votre@email.com">
                </div>
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Bouton suivant -->
            <button type="button" 
                    @click="nextStep"
                    class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
                Continuer <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
        
        <!-- Étape 2 : Sécurité -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-x-4" style="display: none;">
            <!-- Mot de passe -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Mot de passe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           id="password" 
                           class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-300 @enderror"
                           placeholder="••••••••">
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="fas text-lg" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>Minimum 8 caractères, incluant lettres et chiffres
                </p>
            </div>
            
            <!-- Confirmation mot de passe -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirmer le mot de passe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input :type="showPasswordConfirmation ? 'text' : 'password'" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                           placeholder="••••••••">
                    <button type="button" 
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="fas text-lg" :class="showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
            
            <!-- Conditions d'utilisation -->
            <div class="mb-6">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" 
                           x-model="acceptTerms"
                           class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-3 text-sm text-gray-600">
                        J'accepte les 
                        <a href="{{ route('terms') }}" target="_blank" class="text-indigo-600 hover:text-indigo-700">conditions d'utilisation</a>
                        et la 
                        <a href="{{ route('privacy') }}" target="_blank" class="text-indigo-600 hover:text-indigo-700">politique de confidentialité</a>
                        <span class="text-red-500">*</span>
                    </span>
                </label>
            </div>
            
            <!-- Newsletter -->
            <div class="mb-6">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" 
                           name="newsletter" 
                           value="1"
                           checked
                           class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-3 text-sm text-gray-600">
                        Je souhaite recevoir la newsletter et les offres spéciales
                    </span>
                </label>
            </div>
            
            <!-- Navigation -->
            <div class="flex space-x-3">
                <button type="button" 
                        @click="prevStep"
                        class="flex-1 py-3 px-4 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </button>
                <button type="submit" 
                        :disabled="!acceptTerms"
                        class="flex-1 py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus mr-2"></i>S'inscrire
                </button>
            </div>
        </div>
    </form>
    
    <!-- Séparateur -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">Ou s'inscrire avec</span>
        </div>
    </div>
    
    <!-- Inscription sociale -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('social.redirect', 'google') }}" 
           class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors group">
            <i class="fab fa-google text-red-500 mr-2 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-medium text-gray-700">Google</span>
        </a>
        <a href="{{ route('social.redirect', 'github') }}" 
           class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors group">
            <i class="fab fa-github text-gray-800 mr-2 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-medium text-gray-700">GitHub</span>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validation supplémentaire avant soumission
        const form = document.getElementById('register-form');
        
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            
            // Réinitialiser les erreurs
            document.querySelectorAll('.field-error').forEach(el => el.remove());
            
            let hasError = false;
            
            // Validation du mot de passe
            if (password.length < 8) {
                showFieldError('password', 'Le mot de passe doit contenir au moins 8 caractères');
                hasError = true;
            }
            
            if (!/[a-z]/.test(password)) {
                showFieldError('password', 'Le mot de passe doit contenir au moins une minuscule');
                hasError = true;
            }
            
            if (!/[A-Z]/.test(password)) {
                showFieldError('password', 'Le mot de passe doit contenir au moins une majuscule');
                hasError = true;
            }
            
            if (!/[0-9]/.test(password)) {
                showFieldError('password', 'Le mot de passe doit contenir au moins un chiffre');
                hasError = true;
            }
            
            if (password !== confirmation) {
                showFieldError('password_confirmation', 'Les mots de passe ne correspondent pas');
                hasError = true;
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
        
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.classList.add('border-red-300');
            
            const errorDiv = document.createElement('p');
            errorDiv.className = 'field-error text-xs text-red-600 mt-1';
            errorDiv.textContent = message;
            field.parentElement.parentElement.appendChild(errorDiv);
        }
        
        // Force de mot de passe
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                updatePasswordStrength(this.value);
            });
        }
        
        function updatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            let indicator = document.getElementById('password-strength');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'password-strength';
                indicator.className = 'mt-2';
                passwordInput.parentElement.parentElement.appendChild(indicator);
            }
            
            const levels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            const colors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600', 'text-green-700'];
            const barColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'];
            
            indicator.innerHTML = `
                <div class="flex items-center">
                    <div class="flex space-x-1 mr-2">
                        ${Array(5).fill(0).map((_, i) => `
                            <div class="w-8 h-1.5 rounded-full ${i < strength ? barColors[strength-1] || 'bg-gray-400' : 'bg-gray-200'}"></div>
                        `).join('')}
                    </div>
                    <span class="text-xs ${strength > 0 ? colors[strength-1] : 'text-gray-500'}">${strength > 0 ? levels[strength-1] : 'Très faible'}</span>
                </div>
            `;
        }
    });
</script>

<style>
    .error-border {
        border-color: #fca5a5 !important;
    }
    
    .peer:checked + label {
        border-color: #4f46e5;
        background-color: #eef2ff;
    }
</style>
@endpush