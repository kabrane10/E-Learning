<form action="{{ route('admin.settings.social') }}" method="POST">
    @csrf
    
    <div class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook
            </label>
            <input type="url" 
                   name="facebook_url" 
                   value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" 
                   placeholder="https://facebook.com/votre-page"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-twitter text-sky-500 mr-2"></i>Twitter / X
            </label>
            <input type="url" 
                   name="twitter_url" 
                   value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" 
                   placeholder="https://twitter.com/votre-compte"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-linkedin text-blue-700 mr-2"></i>LinkedIn
            </label>
            <input type="url" 
                   name="linkedin_url" 
                   value="{{ old('linkedin_url', $settings['linkedin_url'] ?? '') }}" 
                   placeholder="https://linkedin.com/company/votre-entreprise"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram
            </label>
            <input type="url" 
                   name="instagram_url" 
                   value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" 
                   placeholder="https://instagram.com/votre-compte"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-youtube text-red-600 mr-2"></i>YouTube
            </label>
            <input type="url" 
                   name="youtube_url" 
                   value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" 
                   placeholder="https://youtube.com/@votre-chaine"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-github text-gray-800 mr-2"></i>GitHub
            </label>
            <input type="url" 
                   name="github_url" 
                   value="{{ old('github_url', $settings['github_url'] ?? '') }}" 
                   placeholder="https://github.com/votre-organisation"
                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </div>
</form>