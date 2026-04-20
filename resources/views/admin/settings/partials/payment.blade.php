<form action="{{ route('admin.settings.payment') }}" method="POST">
    @csrf
    
    <div class="space-y-6">
        <!-- Devise -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Devise</label>
                <select name="currency" 
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="EUR" {{ ($settings['currency'] ?? 'EUR') == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                    <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>Dollar US ($)</option>
                    <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>Livre Sterling (£)</option>
                    <option value="CAD" {{ ($settings['currency'] ?? '') == 'CAD' ? 'selected' : '' }}>Dollar Canadien (C$)</option>
                    <option value="CHF" {{ ($settings['currency'] ?? '') == 'CHF' ? 'selected' : '' }}>Franc Suisse (CHF)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Symbole</label>
                <input type="text" 
                       name="currency_symbol" 
                       value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '€') }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        
        <!-- Stripe -->
        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <i class="fab fa-stripe text-2xl text-indigo-600 mr-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">Stripe</h3>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="stripe_enabled" 
                           value="1" 
                           {{ ($settings['stripe_enabled'] ?? false) ? 'checked' : '' }}
                           class="sr-only peer"
                           onchange="toggleStripeFields(this.checked)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Activer</span>
                </label>
            </div>
            
            <div id="stripe-fields" class="{{ ($settings['stripe_enabled'] ?? false) ? '' : 'hidden' }} space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Clé publique (Publishable Key)</label>
                    <input type="text" 
                           name="stripe_key" 
                           value="{{ old('stripe_key', $settings['stripe_key'] ?? '') }}" 
                           placeholder="pk_test_..."
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Clé secrète (Secret Key)</label>
                    <input type="password" 
                           name="stripe_secret" 
                           placeholder="Laisser vide pour ne pas modifier"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Webhook Secret</label>
                    <input type="password" 
                           name="stripe_webhook_secret" 
                           placeholder="whsec_..."
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
        
        <!-- PayPal -->
        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <i class="fab fa-paypal text-2xl text-blue-600 mr-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">PayPal</h3>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="paypal_enabled" 
                           value="1" 
                           {{ ($settings['paypal_enabled'] ?? false) ? 'checked' : '' }}
                           class="sr-only peer"
                           onchange="togglePaypalFields(this.checked)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Activer</span>
                </label>
            </div>
            
            <div id="paypal-fields" class="{{ ($settings['paypal_enabled'] ?? false) ? '' : 'hidden' }} space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode</label>
                    <select name="paypal_mode" 
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="sandbox" {{ ($settings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                        <option value="live" {{ ($settings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>Production</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                    <input type="text" 
                           name="paypal_client_id" 
                           value="{{ old('paypal_client_id', $settings['paypal_client_id'] ?? '') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                    <input type="password" 
                           name="paypal_secret" 
                           placeholder="Laisser vide pour ne pas modifier"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </div>
</form>

<script>
    function toggleStripeFields(enabled) {
        document.getElementById('stripe-fields').classList.toggle('hidden', !enabled);
    }
    
    function togglePaypalFields(enabled) {
        document.getElementById('paypal-fields').classList.toggle('hidden', !enabled);
    }
</script>