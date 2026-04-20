@extends('layouts.guest')

@section('title', 'Vérifier votre email')
@section('auth_title', 'Vérification d\'email')
@section('auth_description', 'Une dernière étape pour sécuriser votre compte.')

@section('content')
<div x-data="{ resent: false }" class="animate-fade-in text-center">
    
    <!-- Icône -->
    <div class="mb-6">
        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto">
            <i class="fas fa-envelope text-indigo-600 text-3xl"></i>
        </div>
    </div>
    
    <!-- Titre -->
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Vérifiez votre email</h1>
    <p class="text-gray-500 mb-6">
        Un lien de vérification a été envoyé à <strong>{{ auth()->user()->email }}</strong>.
    </p>
    
    @if(session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl"
             x-show="!resent"
             x-transition>
            <p class="text-sm text-green-700">
                <i class="fas fa-check-circle mr-2"></i>
                Un nouveau lien de vérification a été envoyé !
            </p>
        </div>
    @endif
    
    <div class="bg-gray-50 rounded-xl p-6 mb-6">
        <p class="text-sm text-gray-600 mb-4">
            <i class="fas fa-info-circle text-indigo-600 mr-1"></i>
            Cliquez sur le lien dans l'email pour vérifier votre compte.
        </p>
        <p class="text-sm text-gray-500">
            Si vous n'avez pas reçu l'email, vérifiez vos spams ou demandez un nouveau lien.
        </p>
    </div>
    
    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}" @submit="resent = true">
            @csrf
            <button type="submit" 
                    class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-paper-plane mr-2"></i>Renvoyer l'email
            </button>
        </form>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full py-3 px-4 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>Se déconnecter
            </button>
        </form>
    </div>
</div>
@endsection