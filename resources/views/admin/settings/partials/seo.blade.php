<form action="{{ route('admin.settings.seo') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
            <input type="text" 
                   name="meta_title" 
                   value="{{ old('meta_title', $settings['meta_title'] ?? '') }}" 
                   maxlength="60"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-xs text-gray-500 mt-1">
                <span id="title-counter">0</span>/60 caractères
            </p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
            <textarea name="meta_description" 
                      rows="3"
                      maxlength="160"
                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">
                <span id="desc-counter">0</span>/160 caractères
            </p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
            <input type="text" 
                   name="meta_keywords" 
                   value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}" 
                   placeholder="mot-clé1, mot-clé2, mot-clé3"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image Open Graph</label>
            @if(isset($settings['og_image']))
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $settings['og_image']) }}" 
                         alt="OG Image" 
                         class="h-24 w-auto border border-gray-200 rounded-lg">
                </div>
            @endif
            <input type="file" 
                   name="og_image" 
                   accept="image/*"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-xs text-gray-500 mt-1">Recommandé : 1200x630px, max 2MB</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                <input type="text" 
                       name="google_analytics_id" 
                       value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" 
                       placeholder="UA-XXXXXXXXX-X ou G-XXXXXXXXXX"
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                <input type="text" 
                       name="facebook_pixel_id" 
                       value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}" 
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">robots.txt</label>
            <textarea name="robots_txt" 
                      rows="6"
                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm">{{ old('robots_txt', $settings['robots_txt'] ?? "User-agent: *\nAllow: /\n\nSitemap: " . url('sitemap.xml')) }}</textarea>
        </div>
        
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.querySelector('[name="meta_title"]');
        const titleCounter = document.getElementById('title-counter');
        const descInput = document.querySelector('[name="meta_description"]');
        const descCounter = document.getElementById('desc-counter');
        
        function updateCounters() {
            if (titleInput && titleCounter) {
                titleCounter.textContent = titleInput.value.length;
            }
            if (descInput && descCounter) {
                descCounter.textContent = descInput.value.length;
            }
        }
        
        if (titleInput) titleInput.addEventListener('input', updateCounters);
        if (descInput) descInput.addEventListener('input', updateCounters);
        
        updateCounters();
    });
</script>