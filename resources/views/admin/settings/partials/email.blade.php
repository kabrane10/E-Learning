<form action="{{ route('admin.settings.email') }}" method="POST">
    @csrf
    
    <div class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Driver d'envoi</label>
            <select name="mail_driver" 
                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="smtp" {{ ($settings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                <option value="ses" {{ ($settings['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                <option value="mailgun" {{ ($settings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                <option value="postmark" {{ ($settings['mail_driver'] ?? '') == 'postmark' ? 'selected' : '' }}>Postmark</option>
            </select>
        </div>
        
        <div id="smtp-settings">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hôte SMTP</label>
                    <input type="text" 
                           name="mail_host" 
                           value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" 
                           placeholder="smtp.gmail.com"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                    <input type="number" 
                           name="mail_port" 
                           value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'utilisateur</label>
                    <input type="text" 
                           name="mail_username" 
                           value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                    <input type="password" 
                           name="mail_password" 
                           placeholder="Laisser vide pour ne pas modifier"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chiffrement</label>
                <select name="mail_encryption" 
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>Aucun</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse d'envoi</label>
                <input type="email" 
                       name="mail_from_address" 
                       value="{{ old('mail_from_address', $settings['mail_from_address'] ?? 'noreply@elearn.com') }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'envoi</label>
                <input type="text" 
                       name="mail_from_name" 
                       value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('app.name')) }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        
        <!-- Test Email -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Tester la configuration</h4>
            <div class="flex space-x-3">
                <input type="email" 
                       id="test-email" 
                       placeholder="email@exemple.com" 
                       class="flex-1 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="button" 
                        onclick="testEmail()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Envoyer un test
                </button>
            </div>
            <p id="test-result" class="text-xs text-gray-500 mt-2"></p>
        </div>
        
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    async function testEmail() {
        const email = document.getElementById('test-email').value;
        const resultEl = document.getElementById('test-result');
        
        if (!email) {
            resultEl.textContent = 'Veuillez entrer une adresse email';
            resultEl.className = 'text-xs text-red-500 mt-2';
            return;
        }
        
        resultEl.textContent = 'Envoi en cours...';
        resultEl.className = 'text-xs text-yellow-500 mt-2';
        
        try {
            // ✅ CORRECTION : Utiliser la route admin complète
            async function testEmail() {
    // ...
    const response = await fetch('/admin/settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ test_email: email })
    });
            
            const data = await response.json();
            
            if (response.ok) {
                resultEl.textContent = '✓ Email envoyé avec succès !';
                resultEl.className = 'text-xs text-green-500 mt-2';
            } else {
                resultEl.textContent = '✗ Erreur : ' + (data.message || 'Échec de l\'envoi');
                resultEl.className = 'text-xs text-red-500 mt-2';
            }
        } catch (error) {
            resultEl.textContent = '✗ Erreur de connexion';
            resultEl.className = 'text-xs text-red-500 mt-2';
        }
    }
</script>
@endpush