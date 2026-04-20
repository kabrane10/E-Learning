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
<div class="py-6" x-data="settingsManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Paramètres</h1>
            <p class="text-gray-500 mt-1">Configurez votre plateforme d'apprentissage</p>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap -mb-px">
                    <button @click="activeTab = 'general'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'general'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-cog mr-2"></i>Général
                    </button>
                    <button @click="activeTab = 'email'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'email', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'email'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </button>
                    <button @click="activeTab = 'payment'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'payment', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'payment'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Paiement
                    </button>
                    <button @click="activeTab = 'security'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'security', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'security'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-shield-alt mr-2"></i>Sécurité
                    </button>
                    <button @click="activeTab = 'social'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'social', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'social'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>Réseaux sociaux
                    </button>
                    <button @click="activeTab = 'seo'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'seo', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'seo'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-search mr-2"></i>SEO
                    </button>
                    <button @click="activeTab = 'maintenance'" 
                            :class="{'border-indigo-600 text-indigo-600': activeTab === 'maintenance', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'maintenance'}"
                            class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                        <i class="fas fa-tools mr-2"></i>Maintenance
                    </button>
                </nav>
            </div>
            
            <!-- Tab Content -->
            <div class="p-6">
                <!-- Général -->
                <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.general')
                </div>
                
                <!-- Email -->
                <div x-show="activeTab === 'email'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.email')
                </div>
                
                <!-- Paiement -->
                <div x-show="activeTab === 'payment'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.payment')
                </div>
                
                <!-- Sécurité -->
                <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.security')
                </div>
                
                <!-- Réseaux sociaux -->
                <div x-show="activeTab === 'social'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.social')
                </div>
                
                <!-- SEO -->
                <div x-show="activeTab === 'seo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.seo')
                </div>
                
                <!-- Maintenance -->
                <div x-show="activeTab === 'maintenance'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    @include('admin.settings.partials.maintenance')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function settingsManager() {
        return {
            activeTab: 'general',
            
            init() {
                // Récupérer l'onglet depuis l'URL (hash)
                const hash = window.location.hash.substring(1);
                if (hash && ['general', 'email', 'payment', 'security', 'social', 'seo', 'maintenance'].includes(hash)) {
                    this.activeTab = hash;
                }
            },
            
            setTab(tab) {
                this.activeTab = tab;
                window.location.hash = tab;
            }
        }
    }
</script>
@endpush