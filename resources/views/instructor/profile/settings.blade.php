@extends('layouts.instructor')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres du compte')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Paramètres</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .toggle-switch {
        appearance: none;
        width: 48px;
        height: 26px;
        background: #d1d5db;
        border-radius: 13px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .toggle-switch:checked {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    
    .toggle-switch::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .toggle-switch:checked::before {
        transform: translateX(22px);
    }
    
    .payment-method-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1);
    }
    
    .payment-method-card.selected {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }
    
    .mobile-money-badge {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }
    
    .bank-badge {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }
    
    .section-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
    }
</style>
@endpush

@section('content')
<div x-data="settingsManager()" class="max-w-4xl mx-auto">
    
    <!-- Message de sauvegarde -->
    <div x-show="saveMessage.show" 
         x-transition
         x-cloak
         class="mb-6 p-4 rounded-xl"
         :class="saveMessage.type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'">
        <div class="flex items-center">
            <i class="fas text-lg mr-3" :class="saveMessage.type === 'success' ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500'"></i>
            <span x-text="saveMessage.text"></span>
        </div>
    </div>

    <div class="space-y-8">
        
        <!-- ============================================ -->
        <!-- NOTIFICATIONS                                -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="section-icon">
                    <i class="fas fa-bell text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Notifications</h3>
                    <p class="text-sm text-gray-500">Gérez vos préférences de notifications</p>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <label class="flex items-center justify-between py-2">
                    <div>
                        <span class="font-medium text-gray-800">Nouvelle inscription à un cours</span>
                        <p class="text-xs text-gray-500">Soyez notifié quand un étudiant s'inscrit</p>
                    </div>
                    <input type="checkbox" x-model="settings.notifications.new_enrollment" class="toggle-switch">
                </label>
                
                <label class="flex items-center justify-between py-2 border-t border-gray-100">
                    <div>
                        <span class="font-medium text-gray-800">Nouvel avis reçu</span>
                        <p class="text-xs text-gray-500">Recevez une notification pour chaque nouvel avis</p>
                    </div>
                    <input type="checkbox" x-model="settings.notifications.new_review" class="toggle-switch">
                </label>
                
                <label class="flex items-center justify-between py-2 border-t border-gray-100">
                    <div>
                        <span class="font-medium text-gray-800">Message d'un étudiant</span>
                        <p class="text-xs text-gray-500">Notifications pour les nouveaux messages</p>
                    </div>
                    <input type="checkbox" x-model="settings.notifications.new_message" class="toggle-switch">
                </label>
                
                <label class="flex items-center justify-between py-2 border-t border-gray-100">
                    <div>
                        <span class="font-medium text-gray-800">Newsletter et offres spéciales</span>
                        <p class="text-xs text-gray-500">Recevez nos actualités et promotions</p>
                    </div>
                    <input type="checkbox" x-model="settings.notifications.newsletter" class="toggle-switch">
                </label>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- PRÉFÉRENCES                                  -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="section-icon">
                    <i class="fas fa-globe text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Préférences régionales</h3>
                    <p class="text-sm text-gray-500">Personnalisez votre expérience</p>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-language mr-2 text-indigo-500"></i>Langue
                        </label>
                        <select x-model="settings.preferences.language" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="ewe">Eʋegbe (Éwé)</option>
                            <option value="fr">Français</option>
                            <option value="en">English</option>
                            <option value="es">Español</option>
                            <option value="it">Italiano</option>

                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-indigo-500"></i>Fuseau horaire
                        </label>
                        <select x-model="settings.preferences.timezone" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Africa/Lome">Lomé (GMT+0)</option>
                            <option value="Africa/Abidjan">Abidjan (GMT+0)</option>
                            <option value="Europe/Paris">Paris (GMT+1/+2)</option>
                            <option value="Europe/London">Londres (GMT+0/+1)</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-2 text-indigo-500"></i>Devise d'affichage
                    </label>
                    <select x-model="settings.preferences.currency" 
                            class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="XOF">FCFA (XOF) - Afrique de l'Ouest</option>
                        <option value="EUR">Euro (€)</option>
                        <option value="USD">Dollar ($)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- INFORMATIONS DE PAIEMENT - TOGO              -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="section-icon">
                    <i class="fas fa-wallet text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Informations de paiement</h3>
                    <p class="text-sm text-gray-500">Configurez comment vous souhaitez recevoir vos revenus</p>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Choix de la méthode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Méthode de retrait préférée
                    </label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Mobile Money -->
                        <div class="payment-method-card p-5 border-2 rounded-xl transition-all"
                             :class="settings.payment.method === 'mobile_money' ? 'selected border-indigo-500' : 'border-gray-200'"
                             @click="settings.payment.method = 'mobile_money'">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900">Mobile Money</span>
                                        <span class="mobile-money-badge text-xs px-2 py-0.5 rounded-full ml-2">Recommandé</span>
                                    </div>
                                </div>
                                <input type="radio" x-model="settings.payment.method" value="mobile_money" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                     :class="settings.payment.method === 'mobile_money' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                    <i class="fas fa-check text-white text-xs" x-show="settings.payment.method === 'mobile_money'"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500">Recevez vos paiements instantanément via T-Money ou Flooz</p>
                            <div class="flex gap-2 mt-3">
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded">T-Money</span>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Flooz</span>
                            </div>
                        </div>
                        
                        <!-- Virement bancaire (bientôt disponible) -->
                        <div class="payment-method-card p-5 border-2 rounded-xl transition-all relative opacity-60 cursor-not-allowed border-gray-200">
                            <div class="absolute top-3 right-3">
                                <span class="bank-badge text-xs px-2 py-1 rounded-full">Bientôt</span>
                            </div>
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-university text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900">Virement bancaire</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500">Virement vers votre compte bancaire (disponible prochainement)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Détails Mobile Money -->
                <div x-show="settings.payment.method === 'mobile_money'" x-transition class="space-y-5 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone-alt mr-2 text-indigo-500"></i>Opérateur Mobile Money
                        </label>
                        <select x-model="settings.payment.mobile_money_provider" 
                                class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="tmoney">T-Money (Togocom)</option>
                            <option value="flooz">Flooz (Moov Africa)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-mobile mr-2 text-indigo-500"></i>Numéro Mobile Money
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-500">+228</span>
                            <input type="tel" 
                                   x-model="settings.payment.mobile_money_number" 
                                   placeholder="90 12 34 56"
                                   maxlength="8"
                                   class="w-full md:w-80 pl-16 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Entrez votre numéro à 8 chiffres sans le préfixe +228
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-indigo-500"></i>Nom complet du titulaire
                        </label>
                        <input type="text" 
                               x-model="settings.payment.mobile_money_name" 
                               placeholder="Ex: KOFFI Yao"
                               class="w-full md:w-80 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-2">
                            Le nom doit correspondre exactement à celui enregistré sur votre compte Mobile Money
                        </p>
                    </div>
                    
                    <!-- Seuil minimum de retrait -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Informations importantes</p>
                                <ul class="text-xs text-amber-700 mt-1 space-y-1">
                                    <li>• Montant minimum de retrait : <strong>5 000 FCFA</strong></li>
                                    <li>• Les retraits sont traités sous 24-48h ouvrées</li>
                                    <li>• Aucun frais de transaction pour les retraits Mobile Money</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SÉCURITÉ DU COMPTE                           -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="section-icon">
                    <i class="fas fa-shield-alt text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Sécurité</h3>
                    <p class="text-sm text-gray-500">Protégez votre compte</p>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-medium text-gray-800">Authentification à deux facteurs (2FA)</span>
                        <p class="text-xs text-gray-500">Ajoutez une couche de sécurité supplémentaire</p>
                    </div>
                    <button class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
                        Configurer
                    </button>
                </div>
                
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div>
                        <span class="font-medium text-gray-800">Sessions actives</span>
                        <p class="text-xs text-gray-500">Gérez vos appareils connectés</p>
                    </div>
                    <button class="px-4 py-2 text-gray-600 hover:text-gray-900 text-sm">
                        Gérer <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ZONE DANGEREUSE                              -->
        <!-- ============================================ -->
        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl border-2 border-red-200 overflow-hidden">
            <div class="px-6 py-5 bg-red-100/50 border-b border-red-200">
                <h3 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Zone dangereuse
                </h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-red-700 mb-4">
                    Une fois que vous supprimez votre compte, toutes vos données seront définitivement effacées. 
                    Cette action est <strong>irréversible</strong>.
                </p>
                <button @click="confirmDeleteAccount" 
                        class="px-5 py-2.5 bg-white border-2 border-red-300 text-red-700 rounded-xl hover:bg-red-600 hover:text-white hover:border-red-600 transition-all duration-200 text-sm font-medium shadow-sm">
                    <i class="fas fa-trash mr-2"></i>Supprimer mon compte
                </button>
            </div>
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex justify-end pt-4">
            <button @click="saveSettings" 
                    class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg font-medium">
                <i class="fas fa-save mr-2"></i>
                Enregistrer tous les paramètres
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
   function settingsManager() {
    return {
        settings: {
            notifications: {
                new_enrollment: true,
                new_review: true,
                new_message: true,
                newsletter: false
            },
            preferences: {
                language: 'fr',
                timezone: 'Africa/Lome',
                currency: 'XOF'
            },
            payment: {
                method: 'mobile_money',
                mobile_money_provider: 'tmoney',
                mobile_money_number: '',
                mobile_money_name: ''
            }
        },
        
        saveMessage: {
            show: false,
            type: 'success',
            text: ''
        },
        
        isLoading: false,
        
        // ✅ Charger les paramètres existants au démarrage
        async init() {
            await this.loadSettings();
        },
        
        async loadSettings() {
            try {
                const response = await fetch('/api/instructor/payment-settings', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Mettre à jour les paramètres de paiement avec les données sauvegardées
                    if (data.payment_method) {
                        this.settings.payment.method = data.payment_method;
                        this.settings.payment.mobile_money_provider = data.mobile_money_provider || 'tmoney';
                        this.settings.payment.mobile_money_number = data.mobile_money_number || '';
                        this.settings.payment.mobile_money_name = data.mobile_money_name || '';
                    }
                    
                    console.log('Paramètres chargés:', this.settings);
                }
            } catch (error) {
                console.error('Erreur chargement paramètres:', error);
            }
        },
        
        // ✅ Sauvegarder les paramètres en base de données
        async saveSettings() {
            this.isLoading = true;
            
            try {
                const response = await fetch('/api/instructor/save-settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.settings)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.saveMessage = {
                        show: true,
                        type: 'success',
                        text: '✅ Paramètres enregistrés avec succès ! Redirection vers le tableau de bord...'
                    };
                    
                    // Redirection après 2 secondes
                    setTimeout(() => {
                        window.location.href = '{{ route("instructor.dashboard") }}';
                    }, 2000);
                } else {
                    this.saveMessage = {
                        show: true,
                        type: 'error',
                        text: '❌ Erreur : ' + (data.message || 'Une erreur est survenue')
                    };
                }
            } catch (error) {
                console.error('Erreur sauvegarde:', error);
                this.saveMessage = {
                    show: true,
                    type: 'error',
                    text: '❌ Erreur de connexion. Veuillez réessayer.'
                };
            } finally {
                this.isLoading = false;
            }
        },
        
        confirmDeleteAccount() {
            if (confirm('ATTENTION : Supprimer définitivement votre compte ?')) {
                if (confirm('Dernière confirmation : Cette action est IRRÉVERSIBLE.')) {
                    alert('Fonctionnalité de suppression à implémenter');
                }
            }
        }
    }
}
</script>
@endpush