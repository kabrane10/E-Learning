@extends('layouts.guest')

@section('title', 'Réinitialiser le mot de passe')
@section('auth_title', 'Nouveau mot de passe')
@section('auth_description', 'Choisissez un nouveau mot de passe sécurisé pour votre compte.')

@section('content')
<div x-data="{ 
    showPassword: false,
    showPasswordConfirmation: false 
}" class="animate-fade-in">
    
    <!-- Titre -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Nouveau mot de passe</h1>
        <p class="text-gray-500 mt-1">
            Choisissez un mot de passe fort pour sécuriser votre compte.
        </p>
    </div>
    
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <ul class="text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Formulaire -->
    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Adresse email
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email', $request->email) }}" 
                       required 
                       readonly
                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl">
            </div>
        </div>
        
        <!-- Nouveau mot de passe -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Nouveau mot de passe
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       id="password" 
                       required
                       class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-300 @enderror"
                       placeholder="••••••••">
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i class="fas text-lg" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>
        
        <!-- Confirmation -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Confirmer le mot de passe
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showPasswordConfirmation ? 'text' : 'password'" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       required
                       class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                       placeholder="••••••••">
                <button type="button" 
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i class="fas text-lg" :class="showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>
        
        <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-save mr-2"></i>Réinitialiser le mot de passe
        </button>
    </form>
</div>
@endsection