<form action="{{ route('admin.settings.general') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du site</label>
                <input type="text" 
                       name="site_name" 
                       value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email de contact</label>
                <input type="email" 
                       name="contact_email" 
                       value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description du site</label>
            <textarea name="site_description" 
                      rows="3"
                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Courte description utilisée pour le SEO et les partages</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mots-clés</label>
            <input type="text" 
                   name="site_keywords" 
                   value="{{ old('site_keywords', $settings['site_keywords'] ?? '') }}" 
                   placeholder="ex: e-learning, cours en ligne, formation"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-xs text-gray-500 mt-1">Séparés par des virgules</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                <input type="text" 
                       name="contact_phone" 
                       value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                <input type="text" 
                       name="address" 
                       value="{{ old('address', $settings['address'] ?? '') }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                @if(isset($settings['site_logo']))
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $settings['site_logo']) }}" 
                             alt="Logo" 
                             class="h-12 w-auto border border-gray-200 rounded-lg p-1">
                    </div>
                @endif
                <input type="file" 
                       name="site_logo" 
                       accept="image/*"
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Format recommandé : PNG, max 2MB</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                @if(isset($settings['site_favicon']))
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $settings['site_favicon']) }}" 
                             alt="Favicon" 
                             class="h-8 w-8 border border-gray-200 rounded">
                    </div>
                @endif
                <input type="file" 
                       name="site_favicon" 
                       accept="image/x-icon,image/png"
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Format : ICO ou PNG, 32x32px</p>
            </div>
        </div>
        
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </div>
</form>