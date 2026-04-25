@extends('layouts.instructor')

@section('title', 'Modifier - ' . $course->title)
@section('page-title', 'Modifier le cours')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-500">Mes Cours</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.show', $course) }}" class="text-gray-400 hover:text-gray-500">{{ Str::limit($course->title, 30) }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Modifier</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .step-indicator { transition: all 0.3s ease; }
    .step-indicator.active { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; }
    .step-indicator.completed { background: #10b981; color: white; }
    .upload-area { border: 2px dashed #d1d5db; transition: all 0.3s ease; }
    .upload-area:hover { border-color: #4f46e5; background: #f5f3ff; }
    .upload-area.dragover { border-color: #4f46e5; background: #e0e7ff; }
    .media-item { transition: all 0.2s ease; }
    .media-item:hover { background-color: #f9fafb; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div x-data="courseEditor({{ Js::from([
    'id' => $course->id,
    'title' => $course->title ?? '',
    'category' => $course->category ?? '',
    'level' => $course->level ?? '',
    'is_free' => $course->is_free ?? true,
    'price' => $course->price ?? 0,
    'short_description' => $course->short_description ?? '',
    'description' => $course->description ?? '',
    'learning_outcomes' => $course->learning_outcomes ?? [''],
    'prerequisites' => $course->prerequisites ?? [''],
    'target_audience' => $course->target_audience ?? '',
    'is_published' => $course->is_published ?? false,
]) }})" x-init="init()">
    
    {{-- Message de soumission --}}
    <div x-show="submitMessage" x-transition x-cloak
         class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm flex items-center">
        <i class="fas fa-spinner fa-spin mr-3"></i>
        <span x-text="submitMessage"></span>
    </div>

    <!-- Indicateur d'étapes -->
    <div class="mb-8">
        <div class="flex items-center justify-center max-w-3xl mx-auto">
            <div class="flex items-center w-full">
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{ 'active': currentStep >= 1, 'completed': currentStep > 1, 'bg-gray-200 text-gray-500': currentStep < 1 }">
                        <span x-show="currentStep <= 1">1</span>
                        <i class="fas fa-check" x-show="currentStep > 1"></i>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 1 ? 'text-indigo-600' : 'text-gray-400'">Infos</span>
                </div>
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 1 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{ 'active': currentStep >= 2, 'completed': currentStep > 2, 'bg-gray-200 text-gray-500': currentStep < 2 }">
                        <span x-show="currentStep <= 2">2</span>
                        <i class="fas fa-check" x-show="currentStep > 2"></i>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 2 ? 'text-indigo-600' : 'text-gray-400'">Description</span>
                </div>
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 2 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{ 'active': currentStep >= 3, 'completed': currentStep > 3, 'bg-gray-200 text-gray-500': currentStep < 3 }">
                        <span x-show="currentStep <= 3">3</span>
                        <i class="fas fa-check" x-show="currentStep > 3"></i>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 3 ? 'text-indigo-600' : 'text-gray-400'">Médias</span>
                </div>
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 3 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{ 'active': currentStep >= 4, 'bg-gray-200 text-gray-500': currentStep < 4 }">
                        <span>4</span>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 4 ? 'text-indigo-600' : 'text-gray-400'">Sauvegarde</span>
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitCourse" class="max-w-4xl mx-auto" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        
        {{-- ========== ÉTAPE 1 : INFOS DE BASE ========== --}}
        <div x-show="currentStep === 1" x-transition>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900"><i class="fas fa-info-circle text-indigo-600 mr-2"></i>Informations de base</h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Titre --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre du cours <span class="text-red-500">*</span></label>
                        <input type="text" x-model="formData.title" required maxlength="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    {{-- Catégorie et Niveau --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie <span class="text-red-500">*</span></label>
                            <select x-model="formData.category" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                                <option value="">Sélectionner</option>
                                <option value="Développement Web">Développement Web</option>
                                <option value="Développement Mobile">Développement Mobile</option>
                                <option value="Data Science">Data Science</option>
                                <option value="Design">Design</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Business">Business</option>
                                <option value="Photographie">Photographie</option>
                                <option value="Musique">Musique</option>
                                <option value="Langues">Langues</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niveau <span class="text-red-500">*</span></label>
                            <select x-model="formData.level" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                                <option value="">Sélectionner</option>
                                <option value="beginner">Débutant</option>
                                <option value="intermediate">Intermédiaire</option>
                                <option value="advanced">Avancé</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Type de cours --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Type de cours</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" @click="formData.is_free = true" 
                                    class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                    :class="formData.is_free ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <i class="fas fa-gift text-2xl mr-3" :class="formData.is_free ? 'text-indigo-600' : 'text-gray-400'"></i>
                                <div class="text-left"><span class="font-medium">Gratuit</span><p class="text-xs text-gray-500">Accès libre</p></div>
                            </button>
                            <button type="button" @click="formData.is_free = false"
                                    class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                    :class="!formData.is_free ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <i class="fas fa-euro-sign text-2xl mr-3" :class="!formData.is_free ? 'text-indigo-600' : 'text-gray-400'"></i>
                                <div class="text-left"><span class="font-medium">Payant</span><p class="text-xs text-gray-500">Définir un prix</p></div>
                            </button>
                        </div>
                    </div>
                    
                    {{-- ✅ Prix (si payant) - CORRIGÉ --}}
                    <div x-show="!formData.is_free" x-transition x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">FCFA</span>
                            <input type="number" x-model="formData.price" min="500" step="500"
                                   x-bind:required="!formData.is_free"
                                   class="w-full pl-20 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimum 500 FCFA • Multiple de 500</p>
                    </div>
                    
                    {{-- Statut de publication --}}
                    <div class="pt-4 border-t border-gray-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="formData.is_published" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Publier ce cours</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">
                            <span x-show="formData.is_published">✅ Le cours est visible par tous les étudiants.</span>
                            <span x-show="!formData.is_published">📝 Le cours est en brouillon, invisible pour les étudiants.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== ÉTAPE 2 : DESCRIPTION ========== --}}
        <div x-show="currentStep === 2" x-transition x-cloak>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b">
                    <h2 class="text-lg font-semibold"><i class="fas fa-align-left text-indigo-600 mr-2"></i>Description détaillée</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description courte <span class="text-red-500">*</span></label>
                        <textarea x-model="formData.short_description" rows="3" required maxlength="200"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none"></textarea>
                        <p class="text-xs text-gray-500 mt-1"><span x-text="formData.short_description.length"></span>/200</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description complète <span class="text-red-500">*</span></label>
                        <textarea x-model="formData.description" rows="8" required class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Objectifs d'apprentissage</label>
                        <div class="space-y-2">
                            <template x-for="(item, index) in formData.learning_outcomes" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="formData.learning_outcomes[index]" class="flex-1 px-4 py-2.5 border rounded-xl" placeholder="Ex: Maîtriser HTML/CSS">
                                    <button type="button" @click="removeLearningOutcome(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                                </div>
                            </template>
                            <button type="button" @click="addLearningOutcome" class="text-indigo-600 text-sm font-medium"><i class="fas fa-plus-circle mr-1"></i>Ajouter un objectif</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Prérequis</label>
                        <div class="space-y-2">
                            <template x-for="(item, index) in formData.prerequisites" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="formData.prerequisites[index]" class="flex-1 px-4 py-2.5 border rounded-xl" placeholder="Ex: Bases en programmation">
                                    <button type="button" @click="removePrerequisite(index)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                                </div>
                            </template>
                            <button type="button" @click="addPrerequisite" class="text-indigo-600 text-sm font-medium"><i class="fas fa-plus-circle mr-1"></i>Ajouter un prérequis</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Public cible</label>
                        <textarea x-model="formData.target_audience" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none" placeholder="Ex: Débutants, professionnels..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== ÉTAPE 3 : MÉDIAS ========== --}}
        <div x-show="currentStep === 3" x-transition x-cloak>
            <div class="space-y-6">
                
                {{-- Image de couverture --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b">
                        <h2 class="text-lg font-semibold"><i class="fas fa-image text-indigo-600 mr-2"></i>Image de couverture</h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Image actuelle :</p>
                            <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=400' }}" class="w-48 h-28 object-cover rounded-lg border">
                        </div>
                        <div class="upload-area rounded-xl p-8 text-center cursor-pointer" @click="$refs.coverInput.click()">
                            <input type="file" x-ref="coverInput" @change="handleCoverSelect" accept="image/*" class="hidden">
                            <template x-if="!formData.thumbnail_preview">
                                <div>
                                    <div class="w-20 h-20 mx-auto mb-4 bg-indigo-100 rounded-full flex items-center justify-center"><i class="fas fa-cloud-upload-alt text-indigo-600 text-3xl"></i></div>
                                    <p class="font-medium">Cliquez pour changer l'image</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, GIF, WEBP • Max 5MB</p>
                                </div>
                            </template>
                            <template x-if="formData.thumbnail_preview">
                                <div>
                                    <img :src="formData.thumbnail_preview" class="max-h-64 mx-auto rounded-lg shadow-md">
                                    <button type="button" @click.stop="removeCover" class="mt-4 text-red-500 text-sm"><i class="fas fa-trash mr-1"></i>Annuler</button>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Laissez vide pour conserver l'image actuelle.</p>
                    </div>
                </div>

                {{-- Vidéo de présentation --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b">
                        <h2 class="text-lg font-semibold"><i class="fas fa-video text-indigo-600 mr-2"></i>Vidéo de présentation</h2>
                    </div>
                    <div class="p-6">
                        @if($course->getFirstMediaUrl('promo_video'))
                            <div class="mb-4 p-3 bg-green-50 rounded-lg flex items-center gap-2 text-green-600 text-sm">
                                <i class="fas fa-check-circle"></i><span>Vidéo déjà présente</span>
                            </div>
                        @endif
                        <div class="upload-area rounded-xl p-8 text-center cursor-pointer" @click="$refs.videoInput.click()">
                            <input type="file" x-ref="videoInput" @change="handleVideoSelect" accept="video/*" class="hidden">
                            <template x-if="!formData.promo_video_name">
                                <div>
                                    <div class="w-20 h-20 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center"><i class="fas fa-video text-purple-600 text-3xl"></i></div>
                                    <p class="font-medium">Changer la vidéo</p>
                                    <p class="text-sm text-gray-500">MP4, MOV, AVI, WEBM</p>
                                    <p class="text-sm text-amber-600 mt-2"><i class="fas fa-exclamation-triangle mr-1"></i><strong>Max 100MB</strong> - L'upload peut prendre du temps</p>
                                </div>
                            </template>
                            <template x-if="formData.promo_video_name">
                                <div>
                                    <div class="flex items-center justify-center gap-3"><i class="fas fa-check-circle text-green-500 text-xl"></i><span class="font-medium" x-text="formData.promo_video_name"></span></div>
                                    <button type="button" @click.stop="removeVideo" class="mt-4 text-red-500 text-sm"><i class="fas fa-trash mr-1"></i>Annuler</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Ressources supplémentaires --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b">
                        <h2 class="text-lg font-semibold"><i class="fas fa-paperclip text-indigo-600 mr-2"></i>Ressources supplémentaires</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Ajoutez ou supprimez des documents</p>
                    </div>
                    <div class="p-6">
                        @php $existingResources = $course->getMedia('resources'); @endphp
                        @if($existingResources->count() > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Ressources actuelles ({{ $existingResources->count() }}) :</p>
                                <div class="space-y-2">
                                    @foreach($existingResources as $media)
                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                            <div class="flex items-center gap-3"><i class="fas fa-file text-green-600"></i><span class="text-sm">{{ $media->file_name }}</span></div>
                                            <button type="button" onclick="deleteExistingResource({{ $media->id }})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2 mb-4">
                            <template x-for="(resource, index) in formData.new_resources" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3"><i class="fas fa-file text-gray-500"></i><span class="text-sm" x-text="resource.name"></span></div>
                                    <button type="button" @click="removeResource(index)" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                                </div>
                            </template>
                        </div>
                        
                        <button type="button" @click="$refs.resourceInput.click()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                            <i class="fas fa-plus mr-2"></i>Ajouter une ressource
                        </button>
                        <input type="file" x-ref="resourceInput" @change="handleResourceSelect" accept=".pdf,.doc,.docx,.txt,.zip,.ppt,.pptx" multiple class="hidden">
                        <p class="text-xs text-gray-500 mt-2">PDF, DOC, TXT, ZIP • <strong>Max 50MB</strong></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== ÉTAPE 4 : SAUVEGARDE ========== --}}
        <div x-show="currentStep === 4" x-transition x-cloak>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b">
                    <h2 class="text-lg font-semibold"><i class="fas fa-check-circle text-indigo-600 mr-2"></i>Prêt à enregistrer</h2>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-xl p-5">
                        <h3 class="font-medium text-gray-900 mb-3">Récapitulatif</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Titre :</span><span class="font-medium" x-text="formData.title"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Catégorie :</span><span class="font-medium" x-text="formData.category"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Type :</span><span class="font-medium"><span x-show="formData.is_free">Gratuit</span><span x-show="!formData.is_free">Payant (<span x-text="formData.price"></span> FCFA)</span></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Statut :</span><span class="font-medium" :class="formData.is_published ? 'text-green-600' : 'text-yellow-600'"><span x-show="formData.is_published">Publié</span><span x-show="!formData.is_published">Brouillon</span></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex justify-between mt-6">
            <button type="button" @click="prevStep" x-show="currentStep > 1" class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Précédent
            </button>
            <div x-show="currentStep === 1" class="flex-1"></div>
            <button type="button" @click="nextStep" x-show="currentStep < 4" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                Suivant <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit" :disabled="isSubmitting" x-show="currentStep === 4"
                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50">
                <i class="fas fa-spinner fa-spin mr-2" x-show="isSubmitting"></i>
                <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function courseEditor(courseData) {
        return {
            currentStep: 1,
            isSubmitting: false,
            submitMessage: '',
            formData: {
                title: courseData.title || '',
                category: courseData.category || '',
                level: courseData.level || '',
                is_free: courseData.is_free !== undefined ? courseData.is_free : true,
                price: courseData.price || 0,
                short_description: courseData.short_description || '',
                description: courseData.description || '',
                learning_outcomes: Array.isArray(courseData.learning_outcomes) ? courseData.learning_outcomes : [''],
                prerequisites: Array.isArray(courseData.prerequisites) ? courseData.prerequisites : [''],
                target_audience: courseData.target_audience || '',
                thumbnail: null,
                thumbnail_preview: null,
                promo_video: null,
                promo_video_name: '',
                new_resources: [],
                is_published: courseData.is_published || false,
            },
            
            init() {
                this.$watch('formData.is_free', value => { if (value) this.formData.price = 0; });
            },
            
            nextStep() { if (this.validateStep(this.currentStep)) { this.currentStep++; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
            prevStep() { this.currentStep--; window.scrollTo({ top: 0, behavior: 'smooth' }); },
            
            validateStep(step) {
                if (step === 1) {
                    if (!this.formData.title) { alert('Veuillez saisir un titre'); return false; }
                    if (!this.formData.category) { alert('Veuillez sélectionner une catégorie'); return false; }
                    if (!this.formData.level) { alert('Veuillez sélectionner un niveau'); return false; }
                    if (!this.formData.is_free && this.formData.price < 500) { alert('Le prix minimum est de 500 FCFA'); return false; }
                }
                if (step === 2) {
                    if (!this.formData.short_description) { alert('Veuillez saisir une description courte'); return false; }
                    if (!this.formData.description) { alert('Veuillez saisir une description complète'); return false; }
                }
                return true;
            },
            
            addLearningOutcome() { this.formData.learning_outcomes.push(''); },
            removeLearningOutcome(i) { if (this.formData.learning_outcomes.length > 1) this.formData.learning_outcomes.splice(i, 1); },
            addPrerequisite() { this.formData.prerequisites.push(''); },
            removePrerequisite(i) { if (this.formData.prerequisites.length > 1) this.formData.prerequisites.splice(i, 1); },
            
            handleCoverSelect(e) { const f = e.target.files[0]; if (f) this.processCover(f); },
            processCover(f) { if (f.size > 5*1024*1024) { alert('Image trop volumineuse (max 5MB)'); return; } this.formData.thumbnail = f; const r = new FileReader(); r.onload = e => this.formData.thumbnail_preview = e.target.result; r.readAsDataURL(f); },
            removeCover() { this.formData.thumbnail = null; this.formData.thumbnail_preview = null; this.$refs.coverInput.value = ''; },
            
            handleVideoSelect(e) { const f = e.target.files[0]; if (f) this.processVideo(f); },
            processVideo(f) { if (f.size > 100*1024*1024) { alert('⚠️ Vidéo trop volumineuse (max 100MB).'); return; } this.formData.promo_video = f; this.formData.promo_video_name = f.name; },
            removeVideo() { this.formData.promo_video = null; this.formData.promo_video_name = ''; this.$refs.videoInput.value = ''; },
            
            handleResourceSelect(e) {
                const files = Array.from(e.target.files);
                for (const f of files) {
                    if (f.size > 50*1024*1024) { alert(`⚠️ "${f.name}" dépasse 50MB`); continue; }
                    this.formData.new_resources.push({ file: f, name: f.name, size: f.size });
                }
                this.$refs.resourceInput.value = '';
            },
            removeResource(i) { this.formData.new_resources.splice(i, 1); },
            
            async submitCourse() {
                if (this.isSubmitting) return;
                if (!this.formData.title || !this.formData.category || !this.formData.level) { alert('Remplissez tous les champs'); return; }
                
                this.isSubmitting = true;
                if (this.formData.promo_video && this.formData.promo_video.size > 50*1024*1024) {
                    this.submitMessage = '⏳ Upload de la vidéo en cours...';
                }
                
                const fd = new FormData();
                fd.append('_method', 'PUT');
                fd.append('title', this.formData.title);
                fd.append('category', this.formData.category);
                fd.append('level', this.formData.level);
                fd.append('is_free', this.formData.is_free ? '1' : '0');
                fd.append('price', this.formData.price);
                fd.append('short_description', this.formData.short_description);
                fd.append('description', this.formData.description);
                fd.append('learning_outcomes', JSON.stringify(this.formData.learning_outcomes.filter(o => o.trim())));
                fd.append('prerequisites', JSON.stringify(this.formData.prerequisites.filter(p => p.trim())));
                fd.append('target_audience', this.formData.target_audience);
                fd.append('is_published', this.formData.is_published ? '1' : '0');
                if (this.formData.thumbnail) fd.append('thumbnail', this.formData.thumbnail);
                if (this.formData.promo_video) fd.append('promo_video', this.formData.promo_video);
                this.formData.new_resources.forEach((r, i) => fd.append(`resources[${i}]`, r.file));
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                try {
                    const controller = new AbortController();
                    const timeout = setTimeout(() => controller.abort(), 300000);
                    const res = await fetch('{{ route("instructor.courses.update", $course) }}', {
                        method: 'POST', body: fd, signal: controller.signal,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    clearTimeout(timeout);
                    
                    if (res.ok) {
                        const data = await res.json();
                        alert('✅ ' + (data.message || 'Cours mis à jour !'));
                        window.location.href = '{{ route("instructor.courses.show", $course) }}';
                    } else {
                        const err = await res.json();
                        alert('❌ ' + (err.message || 'Erreur'));
                    }
                } catch (e) {
                    if (e.name === 'AbortError') alert('❌ Timeout : le fichier est trop volumineux.');
                    else alert('❌ Erreur de connexion.');
                } finally {
                    this.isSubmitting = false;
                    this.submitMessage = '';
                }
            }
        }
    }
    
    function deleteExistingResource(mediaId) {
        if (!confirm('Supprimer cette ressource ?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/instructor/courses/{{ $course->id }}/resources/${mediaId}`;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush