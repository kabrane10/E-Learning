@extends('layouts.instructor')

@section('title', 'Mes Revenus')
@section('page-title', 'Mes Revenus')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Mes Revenus</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="earningsManager()" x-init="init()">
    
    <!-- Solde disponible -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-6 mb-8 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-green-100 text-sm mb-1">Solde disponible</p>
                <p class="text-4xl font-bold" x-text="formatCurrency(stats.available_balance) + ' FCFA'"></p>
                <p class="text-green-100 text-sm mt-2">
                    En attente : <span x-text="formatCurrency(stats.pending_balance) + ' FCFA'"></span>
                </p>
            </div>
            <button @click="openWithdrawModal()" 
                    class="px-6 py-3 bg-white text-green-700 rounded-xl font-medium hover:bg-green-50 transition-colors shadow-md">
                <i class="fas fa-hand-holding-usd mr-2"></i>Retirer
            </button>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase">Total gagné</p>
            <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.total_earned) + ' FCFA'"></p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase">Ce mois</p>
            <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(stats.this_month) + ' FCFA'"></p>
            <p class="text-xs" :class="stats.this_month >= stats.last_month ? 'text-green-600' : 'text-red-600'">
                <i class="fas" :class="stats.this_month >= stats.last_month ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                vs mois dernier
            </p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase">Taux de commission</p>
            <p class="text-2xl font-bold text-gray-900" x-text="stats.commission_rate + '%'"></p>
            <p class="text-xs text-gray-500">Vous recevez <span x-text="stats.commission_rate + '%'"></span> du prix</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase">Total retiré</p>
            <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(totalWithdrawn) + ' FCFA'"></p>
        </div>
    </div>

    <!-- Graphique des revenus -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="font-semibold text-gray-900 mb-4">Évolution des revenus (6 derniers mois)</h3>
        <div style="position: relative; height: 250px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Transactions récentes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-list-alt mr-2 text-indigo-500"></i>Transactions récentes
            </h3>
            <span class="text-sm text-gray-500" x-text="transactions.length + ' transaction(s)'"></span>
        </div>
        
        <template x-if="transactions.length > 0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Commission</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Net</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="transaction in transactions" :key="transaction.id">
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 text-gray-900" x-text="transaction.course"></td>
                                <td class="px-6 py-4 text-center" x-text="formatCurrency(transaction.amount) + ' FCFA'"></td>
                                <td class="px-6 py-4 text-center text-gray-500" x-text="formatCurrency(transaction.commission) + ' FCFA'"></td>
                                <td class="px-6 py-4 text-center font-medium text-gray-900" x-text="formatCurrency(transaction.net) + ' FCFA'"></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="transaction.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                          x-text="transaction.status === 'completed' ? 'Disponible' : 'En attente'"></span>
                                </td>
                                <td class="px-6 py-4 text-gray-500" x-text="transaction.date"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>
        
        <template x-if="transactions.length === 0">
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-receipt text-3xl mb-3 opacity-30"></i>
                <p>Aucune transaction pour le moment</p>
                <p class="text-sm mt-1">Les ventes de vos cours apparaîtront ici.</p>
            </div>
        </template>
    </div>

    <!-- Historique des retraits -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-history mr-2 text-indigo-500"></i>Historique des retraits
            </h3>
            <span class="text-sm text-gray-500" x-text="payouts.length + ' retrait(s)'"></span>
        </div>
        
        <template x-if="payouts.length > 0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="payout in payouts" :key="payout.id">
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900" x-text="formatCurrency(payout.amount) + ' FCFA'"></td>
                                <td class="px-6 py-4 text-gray-600" x-text="payout.method"></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="{
                                              'bg-green-100 text-green-700': payout.status === 'completed',
                                              'bg-yellow-100 text-yellow-700': payout.status === 'pending',
                                              'bg-red-100 text-red-700': payout.status === 'failed'
                                          }"
                                          x-text="payout.status_label"></span>
                                </td>
                                <td class="px-6 py-4 text-gray-500" x-text="payout.date"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>
        
        <template x-if="payouts.length === 0">
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-hand-holding-usd text-3xl mb-3 opacity-30"></i>
                <p>Aucun retrait effectué</p>
                <p class="text-sm mt-1">Vos demandes de retrait apparaîtront ici.</p>
            </div>
        </template>
    </div>

    <!-- Modal Retrait -->
    <div x-show="withdrawModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="withdrawModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-hand-holding-usd text-green-600 mr-2"></i>
                        Retirer des fonds
                    </h3>
                    <p class="text-sm text-gray-500 mt-0.5">Vos fonds seront transférés selon votre méthode préférée</p>
                </div>
                
                <form @submit.prevent="submitWithdraw">
                    <div class="p-6 space-y-5">
                        <!-- Solde disponible -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-wallet mr-2 text-indigo-500"></i>Solde disponible
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-500 font-medium">FCFA</span>
                                <input type="text" 
                                       :value="formatCurrency(stats.available_balance)" 
                                       disabled 
                                       class="w-full pl-20 pr-4 py-3 bg-gray-100 border border-gray-300 rounded-xl text-gray-900 font-medium">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Montant minimum de retrait : <strong>5 000 FCFA</strong>
                            </p>
                        </div>
                        
                        <!-- Montant à retirer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-coins mr-2 text-amber-500"></i>Montant à retirer (FCFA)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-500 font-medium">FCFA</span>
                                <input type="number" 
                                       x-model="withdrawForm.amount" 
                                       min="5000" 
                                       :max="stats.available_balance"
                                       step="500"
                                       required 
                                       class="w-full pl-20 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Ex: 25000">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Saisissez un multiple de 500 FCFA
                            </p>
                        </div>
                        
                        <!-- Méthode de paiement -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-mobile-alt mr-2 text-indigo-500"></i>Méthode de retrait
                            </label>
                            
                            <template x-if="userPaymentMethod === 'mobile_money'">
                                <div>
                                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center">
                                                <i class="fas fa-mobile-alt text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    <span x-text="userMobileMoneyProvider === 'tmoney' ? 'T-Money (Togocom)' : 'Flooz (Moov Africa)'"></span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    +228 <span x-text="formatPhoneNumber(userMobileMoneyNumber)"></span>
                                                </p>
                                                <p class="text-xs text-gray-500" x-text="userMobileMoneyName"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Les fonds seront transférés sur ce compte Mobile Money.
                                        <a href="{{ route('instructor.profile.settings') }}" class="text-indigo-600 hover:text-indigo-700 ml-1">
                                            Modifier dans les paramètres
                                        </a>
                                    </p>
                                </div>
                            </template>
                            
                            <template x-if="!userPaymentMethod || userPaymentMethod === ''">
                                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-red-800">Aucune méthode de paiement configurée</p>
                                            <p class="text-xs text-red-700 mt-1">
                                                Veuillez configurer votre méthode de paiement dans les paramètres avant de demander un retrait.
                                            </p>
                                            <a href="{{ route('instructor.profile.settings') }}" 
                                               class="mt-3 inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                                                <i class="fas fa-cog mr-2"></i>Configurer maintenant
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Délai de traitement -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-clock text-blue-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Délai de traitement</p>
                                    <p class="text-xs text-blue-700">
                                        Les retraits Mobile Money sont traités sous <strong>24 à 48 heures ouvrées</strong>.
                                        Vous recevrez une notification une fois le transfert effectué.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Récapitulatif -->
                        <div x-show="withdrawForm.amount >= 5000" class="bg-gray-50 rounded-xl p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Récapitulatif</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Montant à retirer :</span>
                                    <span class="font-medium text-gray-900" x-text="formatCurrency(withdrawForm.amount) + ' FCFA'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Frais de transaction :</span>
                                    <span class="font-medium text-green-600">Gratuit</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-gray-700 font-medium">Vous recevrez :</span>
                                    <span class="font-bold text-green-600" x-text="formatCurrency(withdrawForm.amount) + ' FCFA'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" 
                                @click="withdrawModalOpen = false" 
                                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                            Annuler
                        </button>
                        <button type="submit" 
                                :disabled="!userPaymentMethod || userPaymentMethod === '' || withdrawForm.amount < 5000 || isSubmitting"
                                class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-spinner fa-spin mr-2" x-show="isSubmitting"></i>
                            <i class="fas fa-paper-plane mr-2" x-show="!isSubmitting"></i>
                            Demander le retrait
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function earningsManager() {
        return {
            // État
            withdrawModalOpen: false,
            isSubmitting: false,
            
            // Données réelles (chargées depuis l'API)
            stats: {
                total_earned: 0,
                available_balance: 0,
                pending_balance: 0,
                this_month: 0,
                last_month: 0,
                commission_rate: 80
            },
            transactions: [],
            payouts: [],
            totalWithdrawn: 0,
            
            // Données de paiement
            userPaymentMethod: '',
            userMobileMoneyProvider: '',
            userMobileMoneyNumber: '',
            userMobileMoneyName: '',
            
            withdrawForm: {
                amount: ''
            },
            
            // Initialisation
            async init() {
                await Promise.all([
                    this.loadStats(),
                    this.loadTransactions(),
                    this.loadPayouts(),
                    this.loadUserPaymentSettings()
                ]);
                
                this.initChart();
            },
            
            // Charger les statistiques
            async loadStats() {
                try {
                    const response = await fetch('/api/instructor/balance');
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.stats.available_balance = data.balance || 0;
                            this.stats.total_earned = data.total_earned || 0;
                            this.stats.this_month = data.this_month || 0;
                            this.stats.last_month = data.last_month || 0;
                            this.stats.pending_balance = data.pending_balance || 0;
                            this.stats.commission_rate = data.commission_rate || 80;
                        }
                    }
                } catch (error) {
                    console.error('Erreur chargement stats:', error);
                }
            },
            
            // Charger les transactions
            async loadTransactions() {
                try {
                    const response = await fetch('/api/instructor/transactions');
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.transactions = data.transactions || [];
                        }
                    }
                } catch (error) {
                    console.error('Erreur chargement transactions:', error);
                }
            },
            
            // Charger l'historique des retraits
            async loadPayouts() {
                try {
                    const response = await fetch('/api/instructor/withdraw-history');
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.payouts = data.payouts || [];
                            this.totalWithdrawn = this.payouts
                                .filter(p => p.status === 'completed')
                                .reduce((sum, p) => sum + p.amount, 0);
                        }
                    }
                } catch (error) {
                    console.error('Erreur chargement retraits:', error);
                }
            },
            
            // Charger les paramètres de paiement
            async loadUserPaymentSettings() {
                try {
                    const response = await fetch('/api/instructor/payment-settings');
                    if (response.ok) {
                        const data = await response.json();
                        this.userPaymentMethod = data.payment_method || '';
                        this.userMobileMoneyProvider = data.mobile_money_provider || '';
                        this.userMobileMoneyNumber = data.mobile_money_number || '';
                        this.userMobileMoneyName = data.mobile_money_name || '';
                    }
                } catch (error) {
                    console.error('Erreur chargement paramètres:', error);
                }
            },
            
            // Ouvrir le modal de retrait
            openWithdrawModal() {
                if (!this.userPaymentMethod) {
                    alert('Veuillez configurer votre méthode de paiement dans les paramètres.');
                    window.location.href = '{{ route("instructor.profile.settings") }}';
                    return;
                }
                
                this.withdrawForm.amount = '';
                this.withdrawModalOpen = true;
            },
            
            // Formater la devise
            formatCurrency(amount) {
                return new Intl.NumberFormat('fr-FR').format(amount || 0);
            },
            
            // Formater le numéro de téléphone
            formatPhoneNumber(number) {
                if (!number) return '';
                return number.replace(/(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4');
            },
            
            // Soumettre la demande de retrait
            async submitWithdraw() {
                if (this.withdrawForm.amount < 5000) {
                    alert('Le montant minimum de retrait est de 5 000 FCFA.');
                    return;
                }
                
                if (this.withdrawForm.amount > this.stats.available_balance) {
                    alert('Le montant demandé dépasse votre solde disponible.');
                    return;
                }
                
                this.isSubmitting = true;
                
                const data = {
                    amount: this.withdrawForm.amount,
                    payment_method: this.userPaymentMethod,
                    mobile_money_provider: this.userMobileMoneyProvider,
                    mobile_money_number: this.userMobileMoneyNumber,
                    mobile_money_name: this.userMobileMoneyName,
                };
                
                try {
                    const response = await fetch('/api/instructor/withdraw', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('✅ Votre demande de retrait de ' + this.formatCurrency(data.amount) + ' FCFA a été enregistrée avec succès !');
                        this.withdrawModalOpen = false;
                        this.isSubmitting = false;
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        alert('❌ Erreur : ' + (result.message || 'Une erreur est survenue'));
                        this.isSubmitting = false;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('❌ Une erreur de connexion est survenue.');
                    this.isSubmitting = false;
                }
            },
            
            // Graphique des revenus
            initChart() {
                const ctx = document.getElementById('revenueChart')?.getContext('2d');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                            datasets: [{
                                label: 'Revenus (FCFA)',
                                data: [125000, 150000, 180000, 210000, 195000, 250000],
                                backgroundColor: '#10b981',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => this.formatCurrency(ctx.raw) + ' FCFA'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => this.formatCurrency(value) + ' FCFA'
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    }
</script>
@endpush