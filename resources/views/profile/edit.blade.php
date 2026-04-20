@extends('layouts.public')

@section('title', 'Profil')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
<!-- Comptes liés -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-link text-indigo-600 mr-2"></i>
            Comptes liés
        </h2>
    </div>
    <div class="p-6 space-y-4">
        <!-- Google -->
        <div class="flex items-center justify-between py-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fab fa-google text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Google</p>
                    @if(auth()->user()->provider === 'google')
                        <p class="text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Compte lié
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Non lié</p>
                    @endif
                </div>
            </div>
            <div>
                @if(auth()->user()->provider === 'google')
                    <form action="{{ route('social.unlink', 'google') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Dissocier votre compte Google ?')"
                                class="text-sm text-red-600 hover:text-red-700">
                            <i class="fas fa-unlink mr-1"></i>Dissocier
                        </button>
                    </form>
                @else
                    <a href="{{ route('social.link', 'google') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-google mr-2 text-red-500"></i>Lier Google
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Facebook -->
        <div class="flex items-center justify-between py-3 border-t border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fab fa-facebook-f text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Facebook</p>
                    @if(auth()->user()->provider === 'facebook')
                        <p class="text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Compte lié
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Non lié</p>
                    @endif
                </div>
            </div>
            <div>
                @if(auth()->user()->provider === 'facebook')
                    <form action="{{ route('social.unlink', 'facebook') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Dissocier votre compte Facebook ?')"
                                class="text-sm text-red-600 hover:text-red-700">
                            <i class="fas fa-unlink mr-1"></i>Dissocier
                        </button>
                    </form>
                @else
                    <a href="{{ route('social.link', 'facebook') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-facebook-f mr-2 text-blue-600"></i>Lier Facebook
                    </a>
                @endif
            </div>
        </div>
        
        <!-- GitHub -->
        <div class="flex items-center justify-between py-3 border-t border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center">
                    <i class="fab fa-github text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">GitHub</p>
                    @if(auth()->user()->provider === 'github')
                        <p class="text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>Compte lié
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Non lié</p>
                    @endif
                </div>
            </div>
            <div>
                @if(auth()->user()->provider === 'github')
                    <form action="{{ route('social.unlink', 'github') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Dissocier votre compte GitHub ?')"
                                class="text-sm text-red-600 hover:text-red-700">
                            <i class="fas fa-unlink mr-1"></i>Dissocier
                        </button>
                    </form>
                @else
                    <a href="{{ route('social.link', 'github') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-github mr-2 text-gray-800"></i>Lier GitHub
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection