<form action="{{ route('admin.settings.security') }}" method="POST">
    @csrf
    
    <div class="space-y-6">
        <!-- reCAPTCHA -->
        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <i class="fas fa-robot text-2xl text-indigo-600 mr-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">Google reCAPTCHA</h3>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="recaptcha_enabled" 
                           value="1" 
                           {{ ($settings['recaptcha_enabled'] ?? false) ? 'checked' : '' }}
                           class="sr-only peer"
                           onchange="toggleRecaptchaFields(this.checked)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Activer</span>
                </label>
            </div>
            
            <div id="recaptcha-fields" class="{{ ($settings['recaptcha_enabled'] ?? false) ? '' : 'hidden' }} space-y-4">
                <p class="text-sm text-gray-500 mb-3">
                    Obtenez vos clés sur 
                    <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-indigo-600 hover:text-indigo-700">
                        Google reCAPTCHA Admin
                    </a>
                </p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Key</label>
                    <input type="text" 
                           name="recaptcha_site_key" 
                           value="{{ old('recaptcha_site_key', $settings['recaptcha_site_key'] ?? '') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                    <input type="password" 
                           name="recaptcha_secret_key" 
                           placeholder="Laisser vide pour ne pas modifier"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
        
        <!-- Authentification -->
        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-lock text-indigo-600 mr-2"></i>Authentification
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Authentification à deux facteurs (2FA)</p>
                        <p class="text-xs text-gray-500">Permettre aux utilisateurs d'activer la 2FA</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="two_factor_enabled" 
                               value="1" 
                               {{ ($settings['two_factor_enabled'] ?? true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durée de session (minutes)</label>
                        <input type="number" 
                               name="session_lifetime" 
                               value="{{ old('session_lifetime', $settings['session_lifetime'] ?? 120) }}" 
                               min="1"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tentatives de connexion max</label>
                        <input type="number" 
                               name="max_login_attempts" 
                               value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}" 
                               min="1"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiration du mot de passe (jours)</label>
                    <input type="number" 
                           name="password_expiry_days" 
                           value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? '') }}" 
                           placeholder="0 = jamais"
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
    function toggleRecaptchaFields(enabled) {
        document.getElementById('recaptcha-fields').classList.toggle('hidden', !enabled);
    }
</script>