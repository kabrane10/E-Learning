@extends('layouts.admin')

@section('title', 'Paramètres')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Paramètres</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Paramètres</h1>
            <p class="text-gray-500 mt-1">Configurez votre plateforme d'apprentissage</p>
        </div>

        <!-- Onglets -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex flex-wrap gap-1">
                @php
                    $currentTab = request()->get('tab', 'general');
                @endphp
                
                <a href="{{ route('admin.settings', ['tab' => 'general']) }}" 
                   class="px-4 py-2 rounded-t-lg text-sm font-medium transition-colors {{ $currentTab === 'general' ? 'bg-white text-indigo-600 border border-gray-200 border-b-white -mb-px' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-globe mr-2"></i>Général
                </a>
                
                <a href="{{ route('admin.settings', ['tab' => 'email']) }}" 
                   class="px-4 py-2 rounded-t-lg text-sm font-medium transition-colors {{ $currentTab === 'email' ? 'bg-white text-indigo-600 border border-gray-200 border-b-white -mb-px' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-envelope mr-2"></i>Email
                </a>
                
                <a href="{{ route('admin.settings', ['tab' => 'payment']) }}" 
                   class="px-4 py-2 rounded-t-lg text-sm font-medium transition-colors {{ $currentTab === 'payment' ? 'bg-white text-indigo-600 border border-gray-200 border-b-white -mb-px' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-credit-card mr-2"></i>Paiement
                </a>
                
                <a href="{{ route('admin.settings', ['tab' => 'security']) }}" 
                   class="px-4 py-2 rounded-t-lg text-sm font-medium transition-colors {{ $currentTab === 'security' ? 'bg-white text-indigo-600 border border-gray-200 border-b-white -mb-px' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-shield-alt mr-2"></i>Sécurité
                </a>
                
                <a href="{{ route('admin.settings', ['tab' => 'social']) }}" 
                   class="px-4 py-2 rounded-t-lg text-sm font-medium transition-colors {{ $currentTab === 'social' ? 'bg-white text-indigo-600 border border-gray-200 border-b-white -mb-px' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-share-alt mr-2"></i>Réseaux sociaux
                </a>
                
                <a href="{{ route('admin.settings', ['tab' => 'seo']) }}" 
                   class="px-4 py-2 rounded-t-lg text-sm font-medium transition-colors {{ $currentTab === 'seo' ? 'bg-white text-indigo-600 border border-gray-200 border-b-white -mb-px' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-search mr-2"></i>SEO
                </a>
            </nav>
        </div>

        <!-- Contenu de l'onglet -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            @php
                $tab = request()->get('tab', 'general');
            @endphp
            
            @if($tab === 'general')
                @include('admin.settings.partials.general')
            @elseif($tab === 'email')
                @include('admin.settings.partials.email')
            @elseif($tab === 'payment')
                @include('admin.settings.partials.payment')
            @elseif($tab === 'security')
                @include('admin.settings.partials.security')
            @elseif($tab === 'social')
                @include('admin.settings.partials.social')
            @elseif($tab === 'seo')
                @include('admin.settings.partials.seo')
            @endif
        </div>
        
    </div>
</div>
@endsection