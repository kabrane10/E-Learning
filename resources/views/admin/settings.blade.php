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
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Paramètres</h1>
            <p class="text-gray-500 mt-1">Configurez votre plateforme d'apprentissage</p>
        </div>
        
        <!-- Onglets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    <button @click="activeTab = 'general'" 
                            :class="activeTab === 'general' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <i class="fas fa-cog mr-2"></i>Général
                    </button>
                    <button @click="activeTab = 'email'" 
                            :class="activeTab === 'email' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </button>
                    <button @click="activeTab = 'payment'" 
                            :class="activeTab === 'payment' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Paiements
                    </button>
                    <button @click="activeTab = 'security'" 
                            :class="activeTab === 'security' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <i class="fas fa-shield-alt mr-2"></i>Sécurité
                    </button>
                    <button @click="activeTab = 'notifications'" 
                            :class="activeTab === 'notifications' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </button>
                    <button @click="activeTab = 'advanced'" 
                            :class="activeTab === 'advanced' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <i class="fas fa-flask mr-2"></i>Avancé
                    </button>
                </nav>
            </div>
            
            <!-- Contenu des onglets -->
            <div class="p-6">
                <!-- Général -->
                <div x-show="activeTab === 'general'" x-cloak>
                    <form @submit.prevent="saveSettings('general')" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la plateforme</label>
                            <input type="text" x-model="settings.general.site_name" 
                                   class="w-full max-w-md rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Apparaîtra dans les titres et les emails</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea x-model="settings.general.site_description" rows="3"
                                      class="w-full max-w-md rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                                <div>
                                    <input type="file" accept="image/*" class="text-sm">
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG ou SVG • Max 2MB</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                            <input type="file" accept="image/x-icon,image/png" class="text-sm">
                            <p class="text-xs text-gray-500 mt-1">Format .ico ou .png • 32x32px recommandé</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 max-w-md">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Langue par défaut</label>
                                <select x-model="settings.general.default_language"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="fr">Français</option>
                                    <option value="en">English</option>
                                    <option value="es">Español</option>
                                    <option value="de">Deutsch</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fuseau horaire</label>
                                <select x-model="settings.general.timezone"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Europe/Paris">Europe/Paris</option>
                                    <option value="Europe/London">Europe/London</option>
                                    <option value="America/New_York">America/New York</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" x-model="settings.general.maintenance_mode" id="maintenance"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="maintenance" class="ml-2 text-sm text-gray-700">Mode maintenance</label>
                            <p class="ml-4 text-xs text-gray-500">Seuls les administrateurs pourront accéder au site</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Email -->
                <div x-show="activeTab === 'email'" x-cloak>
                    <form @submit.prevent="saveSettings('email')" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse email d'envoi</label>
                            <input type="email" x-model="settings.email.from_address" 
                                   class="w-full max-w-md rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="noreply@elearn.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'expéditeur</label>
                            <input type="text" x-model="settings.email.from_name" 
                                   class="w-full max-w-md rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="E-Learn Platform">
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Configuration SMTP</h3>
                            
                            <div class="space-y-4 max-w-md">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hôte SMTP</label>
                                    <input type="text" x-model="settings.email.smtp_host" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="smtp.gmail.com">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                                    <input type="number" x-model="settings.email.smtp_port" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="587">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'utilisateur</label>
                                    <input type="text" x-model="settings.email.smtp_username" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                                    <input type="password" x-model="settings.email.smtp_password" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Chiffrement</label>
                                    <select x-model="settings.email.smtp_encryption"
                                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="">Aucun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="button" @click="testEmail()" 
                                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 mr-3">
                                <i class="fas fa-paper-plane mr-2"></i>Envoyer un email de test
                            </button>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Paiements -->
                <div x-show="activeTab === 'payment'" x-cloak>
                    <form @submit.prevent="saveSettings('payment')" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Devise</label>
                            <select x-model="settings.payment.currency"
                                    class="w-full max-w-md rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="EUR">Euro (€)</option>
                                <option value="USD">Dollar US ($)</option>
                                <option value="GBP">Livre Sterling (£)</option>
                                <option value="CAD">Dollar Canadien (C$)</option>
                            </select>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Stripe</h3>
                            
                            <div class="space-y-4 max-w-md">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" x-model="settings.payment.stripe_enabled" id="stripe_enabled"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="stripe_enabled" class="ml-2 text-sm text-gray-700 font-medium">Activer Stripe</label>
                                </div>
                                
                                <div x-show="settings.payment.stripe_enabled">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Clé publique</label>
                                    <input type="text" x-model="settings.payment.stripe_key" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="pk_live_...">
                                </div>
                                
                                <div x-show="settings.payment.stripe_enabled">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Clé secrète</label>
                                    <input type="password" x-model="settings.payment.stripe_secret" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="sk_live_...">
                                </div>
                                
                                <div x-show="settings.payment.stripe_enabled">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Webhook Secret</label>
                                    <input type="password" x-model="settings.payment.stripe_webhook" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="whsec_...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">PayPal</h3>
                            
                            <div class="space-y-4 max-w-md">
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" x-model="settings.payment.paypal_enabled" id="paypal_enabled"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="paypal_enabled" class="ml-2 text-sm text-gray-700 font-medium">Activer PayPal</label>
                                </div>
                                
                                <div x-show="settings.payment.paypal_enabled">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                                    <input type="text" x-model="settings.payment.paypal_client_id" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div x-show="settings.payment.paypal_enabled">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                                    <input type="password" x-model="settings.payment.paypal_secret" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div x-show="settings.payment.paypal_enabled">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode</label>
                                    <select x-model="settings.payment.paypal_mode"
                                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="sandbox">Sandbox (Test)</option>
                                        <option value="live">Live (Production)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Sécurité -->
                <div x-show="activeTab === 'security'" x-cloak>
                    <form @submit.prevent="saveSettings('security')" class="space-y-6">
                        <div class="max-w-md space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Durée de session (minutes)</label>
                                <input type="number" x-model="settings.security.session_lifetime" 
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                       value="120">
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" x-model="settings.security.two_factor_auth" id="2fa"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="2fa" class="ml-2 text-sm text-gray-700">Authentification à deux facteurs obligatoire</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" x-model="settings.security.force_https" id="https"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="https" class="ml-2 text-sm text-gray-700">Forcer HTTPS</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" x-model="settings.security.registration_enabled" id="registration"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="registration" class="ml-2 text-sm text-gray-700">Autoriser les inscriptions</label>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tentatives de connexion max</label>
                                <input type="number" x-model="settings.security.max_login_attempts" 
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                       value="5">
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Notifications -->
                <div x-show="activeTab === 'notifications'" x-cloak>
                    <form @submit.prevent="saveSettings('notifications')" class="space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between max-w-md">
                                <span class="text-sm text-gray-700">Nouvelle inscription</span>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.new_user_email" class="rounded mr-2"> Email
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.new_user_push" class="rounded mr-2"> Push
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between max-w-md">
                                <span class="text-sm text-gray-700">Cours terminé</span>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.course_completed_email" class="rounded mr-2"> Email
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.course_completed_push" class="rounded mr-2"> Push
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between max-w-md">
                                <span class="text-sm text-gray-700">Quiz réussi</span>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.quiz_passed_email" class="rounded mr-2"> Email
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.quiz_passed_push" class="rounded mr-2"> Push
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between max-w-md">
                                <span class="text-sm text-gray-700">Rappel de cours inactif</span>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.reminder_email" class="rounded mr-2"> Email
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between max-w-md">
                                <span class="text-sm text-gray-700">Nouveau message</span>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.new_message_email" class="rounded mr-2"> Email
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="settings.notifications.new_message_push" class="rounded mr-2"> Push
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Avancé -->
                <div x-show="activeTab === 'advanced'" x-cloak>
                    <form @submit.prevent="saveSettings('advanced')" class="space-y-6">
                        <div class="max-w-md space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Clé API</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" x-model="settings.advanced.api_key" readonly
                                           class="flex-1 rounded-lg border-gray-300 bg-gray-50">
                                    <button type="button" @click="regenerateApiKey()" 
                                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cache</label>
                                <button type="button" @click="clearCache()" 
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                    <i class="fas fa-trash mr-2"></i>Vider le cache
                                </button>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                                <input type="text" x-model="settings.advanced.ga_id" 
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="UA-XXXXXXXXX-X">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                                <input type="text" x-model="settings.advanced.fb_pixel_id" 
                                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="XXXXXXXXXXXXXXX">
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" x-model="settings.advanced.debug_mode" id="debug"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="debug" class="ml-2 text-sm text-gray-700">Mode débogage</label>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    function settingsManager() {
        return {
            activeTab: 'general',
            
            settings: {
                general: {
                    site_name: 'E-Learn Platform',
                    site_description: 'Plateforme d\'apprentissage en ligne',
                    default_language: 'fr',
                    timezone: 'Europe/Paris',
                    maintenance_mode: false
                },
                email: {
                    from_address: 'noreply@elearn.com',
                    from_name: 'E-Learn Platform',
                    smtp_host: '',
                    smtp_port: 587,
                    smtp_username: '',
                    smtp_password: '',
                    smtp_encryption: 'tls'
                },
                payment: {
                    currency: 'EUR',
                    stripe_enabled: false,
                    stripe_key: '',
                    stripe_secret: '',
                    stripe_webhook: '',
                    paypal_enabled: false,
                    paypal_client_id: '',
                    paypal_secret: '',
                    paypal_mode: 'sandbox'
                },
                security: {
                    session_lifetime: 120,
                    two_factor_auth: false,
                    force_https: true,
                    registration_enabled: true,
                    max_login_attempts: 5
                },
                notifications: {
                    new_user_email: true,
                    new_user_push: false,
                    course_completed_email: true,
                    course_completed_push: true,
                    quiz_passed_email: true,
                    quiz_passed_push: true,
                    reminder_email: true,
                    new_message_email: true,
                    new_message_push: true
                },
                advanced: {
                    api_key: 'sk_live_' + Math.random().toString(36).substring(2, 15),
                    ga_id: '',
                    fb_pixel_id: '',
                    debug_mode: false
                }
            },
            
            saveSettings(section) {
                console.log('Sauvegarde de la section:', section, this.settings[section]);
                alert('Paramètres "' + section + '" enregistrés avec succès !');
            },
            
            testEmail() {
                const email = prompt('Adresse email pour le test:');
                if (email) {
                    alert('Email de test envoyé à ' + email);
                }
            },
            
            regenerateApiKey() {
                this.settings.advanced.api_key = 'sk_live_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
                alert('Nouvelle clé API générée');
            },
            
            clearCache() {
                if (confirm('Vider le cache de l\'application ?')) {
                    alert('Cache vidé avec succès');
                }
            }
        }
    }
</script>
@endpush