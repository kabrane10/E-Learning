@extends('layouts.instructor')

@section('title', 'Créer un nouveau cours')
@section('page-title', 'Créer un nouveau cours')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-500">Mes Cours</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Créer un cours</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .step-indicator {
        transition: all 0.3s ease;
    }
    
    .step-indicator.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
    }
    
    .step-indicator.completed {
        background: #10b981;
        color: white;
    }
    
    .upload-area {
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        border-color: #4f46e5;
        background: #f5f3ff;
    }
    
    .upload-area.dragover {
        border-color: #4f46e5;
        background: #e0e7ff;
    }
    
    .media-item {
        transition: all 0.2s ease;
    }
    
    .media-item:hover {
        background-color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div x-data="courseCreator()" x-init="init()">
    
    <!-- Indicateur d'étapes -->
    <div class="mb-8">
        <div class="flex items-center justify-center max-w-3xl mx-auto">
            <div class="flex items-center w-full">
                <!-- Étape 1 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{
                            'active': currentStep >= 1,
                            'completed': currentStep > 1,
                            'bg-gray-200 text-gray-500': currentStep < 1
                         }">
                        <span x-show="currentStep <= 1">1</span>
                        <i class="fas fa-check" x-show="currentStep > 1"></i>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 1 ? 'text-indigo-600' : 'text-gray-400'">
                        Infos
                    </span>
                </div>
                
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 1 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                
                <!-- Étape 2 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{
                            'active': currentStep >= 2,
                            'completed': currentStep > 2,
                            'bg-gray-200 text-gray-500': currentStep < 2
                         }">
                        <span x-show="currentStep <= 2">2</span>
                        <i class="fas fa-check" x-show="currentStep > 2"></i>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 2 ? 'text-indigo-600' : 'text-gray-400'">
                        Description
                    </span>
                </div>
                
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 2 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                
                <!-- Étape 3 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{
                            'active': currentStep >= 3,
                            'completed': currentStep > 3,
                            'bg-gray-200 text-gray-500': currentStep < 3
                         }">
                        <span x-show="currentStep <= 3">3</span>
                        <i class="fas fa-check" x-show="currentStep > 3"></i>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 3 ? 'text-indigo-600' : 'text-gray-400'">
                        Médias
                    </span>
                </div>
                
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 3 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                
                <!-- Étape 4 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm"
                         :class="{
                            'active': currentStep >= 4,
                            'bg-gray-200 text-gray-500': currentStep < 4
                         }">
                        <span>4</span>
                    </div>
                    <span class="text-xs mt-2 font-medium" :class="currentStep >= 4 ? 'text-indigo-600' : 'text-gray-400'">
                        Publication
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <form @submit.prevent="submitCourse" class="max-w-4xl mx-auto" enctype="multipart/form-data" novalidate>
        
        <!-- ============================================ -->
        <!-- ÉTAPE 1 : INFORMATIONS DE BASE                -->
        <!-- ============================================ -->
        <div x-show="currentStep === 1" x-transition>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                        Informations de base
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Donnez un titre, une catégorie et définissez le type de votre cours</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Titre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Titre du cours <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               x-model="formData.title" 
                               required
                               maxlength="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                               placeholder="Ex: Développement Web Complet 2024">
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-text="formData.title.length"></span>/100 caractères
                        </p>
                    </div>
                    
                    <!-- Catégorie et Niveau -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catégorie <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.category" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner une catégorie</option>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Niveau <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.level" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner un niveau</option>
                                <option value="beginner">Débutant</option>
                                <option value="intermediate">Intermédiaire</option>
                                <option value="advanced">Avancé</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Type de cours -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Type de cours
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                   @click="formData.is_free = true"
                                   :class="formData.is_free ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" x-model="formData.is_free" :value="true" class="sr-only">
                                <i class="fas fa-gift text-2xl mr-3" :class="formData.is_free ? 'text-indigo-600' : 'text-gray-400'"></i>
                                <div>
                                    <span class="font-medium" :class="formData.is_free ? 'text-indigo-600' : 'text-gray-700'">Gratuit</span>
                                    <p class="text-xs text-gray-500">Accès libre pour tous</p>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                   @click="formData.is_free = false"
                                   :class="!formData.is_free ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" x-model="formData.is_free" :value="false" class="sr-only">
                                <i class="fas fa-euro-sign text-2xl mr-3" :class="!formData.is_free ? 'text-indigo-600' : 'text-gray-400'"></i>
                                <div>
                                    <span class="font-medium" :class="!formData.is_free ? 'text-indigo-600' : 'text-gray-700'">Payant</span>
                                    <p class="text-xs text-gray-500">Définir un prix</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Prix (si payant) -->
                    <div x-show="!formData.is_free" x-transition class="pt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Prix du cours (€) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">€</span>
                            <input type="number" 
                                   x-model="formData.price" 
                                   min="0.99" 
                                   step="0.01"
                                   :required="!formData.is_free"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="49.99">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Les étudiants devront payer ce montant pour accéder au cours. Vous recevrez 80% du prix (commission de 20%).
                        </p>
                        <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-sm text-amber-800">
                                <i class="fas fa-lock mr-2"></i>
                                <strong>Cours payant :</strong> L'accès sera restreint aux étudiants ayant effectué le paiement.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Cours gratuit - message -->
                    <div x-show="formData.is_free" x-transition class="pt-2">
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Cours gratuit :</strong> Tous les étudiants pourront s'inscrire librement.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 2 : DESCRIPTION DÉTAILLÉE              -->
        <!-- ============================================ -->
        <div x-show="currentStep === 2" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-align-left text-indigo-600 mr-2"></i>
                        Description détaillée
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Décrivez votre cours pour attirer les étudiants</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Description courte -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description courte <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="formData.short_description" 
                                  rows="3" 
                                  required
                                  maxlength="200"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                  placeholder="Un résumé accrocheur de votre cours (visible dans les résultats de recherche)"></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-text="formData.short_description.length"></span>/200 caractères
                        </p>
                    </div>
                    
                    <!-- Description complète -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description complète <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="formData.description" 
                                  rows="8" 
                                  required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                  placeholder="Décrivez en détail ce que les étudiants apprendront..."></textarea>
                    </div>
                    
                    <!-- Ce que les étudiants apprendront -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Ce que les étudiants apprendront
                        </label>
                        <div class="space-y-2">
                            <template x-for="(item, index) in formData.learning_outcomes" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                           x-model="formData.learning_outcomes[index]" 
                                           class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Ex: Maîtriser les bases de HTML/CSS">
                                    <button type="button" 
                                            @click="removeLearningOutcome(index)"
                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" 
                                    @click="addLearningOutcome"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                <i class="fas fa-plus-circle mr-1"></i>Ajouter un objectif
                            </button>
                        </div>
                    </div>
                    
                    <!-- Prérequis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Prérequis
                        </label>
                        <div class="space-y-2">
                            <template x-for="(item, index) in formData.prerequisites" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                           x-model="formData.prerequisites[index]" 
                                           class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Ex: Connaissances de base en programmation">
                                    <button type="button" 
                                            @click="removePrerequisite(index)"
                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" 
                                    @click="addPrerequisite"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                <i class="fas fa-plus-circle mr-1"></i>Ajouter un prérequis
                            </button>
                        </div>
                    </div>
                    
                    <!-- Public cible -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Public cible
                        </label>
                        <textarea x-model="formData.target_audience" 
                                  rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                  placeholder="À qui s'adresse ce cours ? (débutants, professionnels, etc.)"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 3 : MÉDIAS (IMAGES, VIDÉOS, DOCUMENTS)  -->
        <!-- ============================================ -->
        <div x-show="currentStep === 3" x-transition style="display: none;">
            <div class="space-y-6">
                <!-- Image de couverture -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-image text-indigo-600 mr-2"></i>
                            Image de couverture
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Ajoutez une image attrayante pour votre cours</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="upload-area rounded-xl p-8 text-center cursor-pointer"
                             @click="$refs.coverInput.click()"
                             @dragover.prevent="$el.classList.add('dragover')"
                             @dragleave.prevent="$el.classList.remove('dragover')"
                             @drop.prevent="handleCoverDrop($event)">
                            
                            <input type="file" 
                                   x-ref="coverInput" 
                                   @change="handleCoverSelect" 
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   class="hidden">
                            
                            <template x-if="!formData.thumbnail_preview">
                                <div>
                                    <div class="w-20 h-20 mx-auto mb-4 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-cloud-upload-alt text-indigo-600 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-700 font-medium mb-1">
                                        Glissez-déposez ou cliquez pour uploader
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        PNG, JPG, GIF ou WEBP • Max 5MB
                                    </p>
                                    <p class="text-sm text-gray-400 mt-2">
                                        Taille recommandée : 1280 x 720 pixels
                                    </p>
                                </div>
                            </template>
                            
                            <template x-if="formData.thumbnail_preview">
                                <div>
                                    <img :src="formData.thumbnail_preview" 
                                         class="max-h-64 mx-auto rounded-lg shadow-md">
                                    <button type="button" 
                                            @click.stop="removeCover"
                                            class="mt-4 text-red-500 hover:text-red-700 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Supprimer l'image
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Vidéo de présentation (optionnelle) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-video text-indigo-600 mr-2"></i>
                            Vidéo de présentation (optionnelle)
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Ajoutez une vidéo d'introduction pour votre cours</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="upload-area rounded-xl p-8 text-center cursor-pointer"
                             @click="$refs.videoInput.click()"
                             @dragover.prevent="$el.classList.add('dragover')"
                             @dragleave.prevent="$el.classList.remove('dragover')"
                             @drop.prevent="handleVideoDrop($event)">
                            
                            <input type="file" 
                                   x-ref="videoInput" 
                                   @change="handleVideoSelect" 
                                   accept="video/mp4,video/mov,video/avi,video/webm"
                                   class="hidden">
                            
                            <template x-if="!formData.promo_video_name">
                                <div>
                                    <div class="w-20 h-20 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-video text-purple-600 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-700 font-medium mb-1">
                                        Ajouter une vidéo de présentation
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        MP4, MOV, AVI ou WEBM • Max 500MB
                                    </p>
                                </div>
                            </template>
                            
                            <template x-if="formData.promo_video_name">
                                <div>
                                    <div class="flex items-center justify-center gap-3">
                                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                        <span class="font-medium" x-text="formData.promo_video_name"></span>
                                    </div>
                                    <button type="button" 
                                            @click.stop="removeVideo"
                                            class="mt-4 text-red-500 hover:text-red-700 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Supprimer la vidéo
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Ressources supplémentaires (PDF, documents) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-paperclip text-indigo-600 mr-2"></i>
                            Ressources supplémentaires (optionnel)
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Ajoutez des documents, PDF ou autres ressources</p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Liste des ressources -->
                        <div class="space-y-2 mb-4">
                            <template x-for="(resource, index) in formData.resources" :key="index">
                                <div class="media-item flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file text-gray-500"></i>
                                        <span class="text-sm" x-text="resource.name"></span>
                                        <span class="text-xs text-gray-400" x-text="formatFileSize(resource.size)"></span>
                                    </div>
                                    <button type="button" 
                                            @click="removeResource(index)"
                                            class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            
                            <div x-show="formData.resources.length === 0" class="text-center py-4 text-gray-400">
                                <i class="fas fa-file-alt text-2xl mb-2"></i>
                                <p class="text-sm">Aucune ressource ajoutée</p>
                            </div>
                        </div>
                        
                        <!-- Bouton d'ajout -->
                        <button type="button" 
                                @click="$refs.resourceInput.click()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                            <i class="fas fa-plus mr-2"></i>Ajouter une ressource
                        </button>
                        <input type="file" 
                               x-ref="resourceInput" 
                               @change="handleResourceSelect" 
                               accept=".pdf,.doc,.docx,.txt,.zip"
                               multiple
                               class="hidden">
                        <p class="text-xs text-gray-500 mt-2">
                            Formats acceptés : PDF, DOC, DOCX, TXT, ZIP • Max 50MB par fichier
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 4 : PUBLICATION                        -->
        <!-- ============================================ -->
        <div x-show="currentStep === 4" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-check-circle text-indigo-600 mr-2"></i>
                        Prêt à publier
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Dernière étape avant la publication</p>
                </div>
                
                <div class="p-6">
                    <!-- Récapitulatif -->
                    <div class="bg-gray-50 rounded-xl p-5 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Récapitulatif de votre cours</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Titre :</span>
                                <span class="font-medium text-gray-900" x-text="formData.title || 'Non défini'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Catégorie :</span>
                                <span class="font-medium text-gray-900" x-text="formData.category || 'Non définie'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Niveau :</span>
                                <span class="font-medium text-gray-900">
                                    <span x-show="formData.level === 'beginner'">Débutant</span>
                                    <span x-show="formData.level === 'intermediate'">Intermédiaire</span>
                                    <span x-show="formData.level === 'advanced'">Avancé</span>
                                    <span x-show="!formData.level">Non défini</span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type :</span>
                                <span class="font-medium text-gray-900">
                                    <span x-show="formData.is_free">Gratuit</span>
                                    <span x-show="!formData.is_free">Payant (<span x-text="formData.price"></span> €)</span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Image de couverture :</span>
                                <span class="font-medium" :class="formData.thumbnail ? 'text-green-600' : 'text-red-500'">
                                    <span x-show="formData.thumbnail">✅ Ajoutée</span>
                                    <span x-show="!formData.thumbnail">❌ Manquante</span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Vidéo de présentation :</span>
                                <span class="font-medium" :class="formData.promo_video ? 'text-green-600' : 'text-gray-500'">
                                    <span x-show="formData.promo_video">✅ Ajoutée</span>
                                    <span x-show="!formData.promo_video">Non ajoutée</span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Ressources :</span>
                                <span class="font-medium text-gray-900">
                                    <span x-text="formData.resources.length"></span> fichier(s)
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Options de publication -->
                    <div class="space-y-4">
                        <label class="flex items-start p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" x-model="formData.publish_action" value="publish" class="mt-1 mr-3">
                            <div>
                                <span class="font-medium text-gray-900">Publier maintenant</span>
                                <p class="text-sm text-gray-500">Votre cours sera visible par tous les étudiants</p>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" x-model="formData.publish_action" value="draft" class="mt-1 mr-3">
                            <div>
                                <span class="font-medium text-gray-900">Enregistrer comme brouillon</span>
                                <p class="text-sm text-gray-500">Vous pourrez le publier plus tard</p>
                            </div>
                        </label>
                        
                        <label class="flex items-start p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" x-model="formData.publish_action" value="schedule" class="mt-1 mr-3">
                            <div>
                                <span class="font-medium text-gray-900">Programmer la publication</span>
                                <p class="text-sm text-gray-500">Choisissez une date de publication</p>
                            </div>
                        </label>
                        
                        <div x-show="formData.publish_action === 'schedule'" x-transition class="pl-8">
                            <input type="datetime-local" 
                                   x-model="formData.scheduled_at"
                                   :min="new Date().toISOString().slice(0, 16)"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mt-6">
            <button type="button" 
                    @click="prevStep"
                    class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
                    x-show="currentStep > 1">
                <i class="fas fa-arrow-left mr-2"></i>Précédent
            </button>
            <div x-show="currentStep === 1" class="flex-1"></div>
            
            <button type="button" 
                    @click="nextStep"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm"
                    x-show="currentStep < 4">
                Suivant <i class="fas fa-arrow-right ml-2"></i>
            </button>
            
            <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md"
                    x-show="currentStep === 4">
                <i class="fas fa-check mr-2"></i>
                <span x-text="formData.publish_action === 'publish' ? 'Publier le cours' : 'Enregistrer'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function courseCreator() {
        return {
            currentStep: 1,
            formData: {
                title: '',
                category: '',
                level: '',
                is_free: true,
                price: 0,
                short_description: '',
                description: '',
                learning_outcomes: [''],
                prerequisites: [''],
                target_audience: '',
                thumbnail: null,
                thumbnail_preview: null,
                promo_video: null,
                promo_video_name: '',
                resources: [],
                publish_action: 'draft',
                scheduled_at: null,
            },
            
            init() {
                console.log('Créateur de cours initialisé');
                
                this.$watch('formData.is_free', (value) => {
                    if (value) {
                        this.formData.price = 0;
                    }
                });
            },
            
            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
            
            prevStep() {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            
            validateStep(step) {
                switch(step) {
                    case 1:
                        if (!this.formData.title) {
                            alert('Veuillez saisir un titre');
                            return false;
                        }
                        if (!this.formData.category) {
                            alert('Veuillez sélectionner une catégorie');
                            return false;
                        }
                        if (!this.formData.level) {
                            alert('Veuillez sélectionner un niveau');
                            return false;
                        }
                        if (!this.formData.is_free && (!this.formData.price || this.formData.price < 0.99)) {
                            alert('Veuillez définir un prix minimum de 0.99€');
                            return false;
                        }
                        return true;
                        
                    case 2:
                        if (!this.formData.short_description) {
                            alert('Veuillez saisir une description courte');
                            return false;
                        }
                        if (!this.formData.description) {
                            alert('Veuillez saisir une description complète');
                            return false;
                        }
                        return true;
                        
                    default:
                        return true;
                }
            },
            
            addLearningOutcome() {
                this.formData.learning_outcomes.push('');
            },
            
            removeLearningOutcome(index) {
                if (this.formData.learning_outcomes.length > 1) {
                    this.formData.learning_outcomes.splice(index, 1);
                }
            },
            
            addPrerequisite() {
                this.formData.prerequisites.push('');
            },
            
            removePrerequisite(index) {
                if (this.formData.prerequisites.length > 1) {
                    this.formData.prerequisites.splice(index, 1);
                }
            },
            
            // Gestion de l'image de couverture
            handleCoverSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processCover(file);
                }
            },
            
            handleCoverDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.processCover(file);
                }
                event.target.classList.remove('dragover');
            },
            
            processCover(file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux (max 5MB)');
                    return;
                }
                
                this.formData.thumbnail = file;
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.formData.thumbnail_preview = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            
            removeCover() {
                this.formData.thumbnail = null;
                this.formData.thumbnail_preview = null;
                this.$refs.coverInput.value = '';
            },
            
            // Gestion de la vidéo de présentation
            handleVideoSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processVideo(file);
                }
            },
            
            handleVideoDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('video/')) {
                    this.processVideo(file);
                }
                event.target.classList.remove('dragover');
            },
            
            processVideo(file) {
                if (file.size > 500 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux (max 500MB)');
                    return;
                }
                
                this.formData.promo_video = file;
                this.formData.promo_video_name = file.name;
            },
            
            removeVideo() {
                this.formData.promo_video = null;
                this.formData.promo_video_name = '';
                this.$refs.videoInput.value = '';
            },
            
            // Gestion des ressources
            handleResourceSelect(event) {
                const files = Array.from(event.target.files);
                
                for (const file of files) {
                    if (file.size > 50 * 1024 * 1024) {
                        alert(`Le fichier "${file.name}" est trop volumineux (max 50MB)`);
                        continue;
                    }
                    
                    this.formData.resources.push({
                        file: file,
                        name: file.name,
                        size: file.size
                    });
                }
                
                this.$refs.resourceInput.value = '';
            },
            
            removeResource(index) {
                this.formData.resources.splice(index, 1);
            },
            
            formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(1) + ' MB';
            },
            
        async submitCourse() {
    // Validation
    if (!this.formData.title || !this.formData.category || !this.formData.level) {
        alert('Veuillez remplir tous les champs obligatoires');
        this.currentStep = 1;
        return;
    }
    
    if (!this.formData.short_description || !this.formData.description) {
        alert('Veuillez remplir la description');
        this.currentStep = 2;
        return;
    }
    
    // Afficher un indicateur de chargement
     // ✅ Afficher un indicateur
    this.isSubmitting = true;
    this.submitMessage = 'Envoi en cours... (cela peut prendre quelques instants pour les fichiers volumineux)';
    
    // ✅ Créer FormData pour l'envoi RÉEL
    const formData = new FormData();
    formData.append('title', this.formData.title);
    formData.append('category', this.formData.category);
    formData.append('level', this.formData.level);
    formData.append('is_free', this.formData.is_free ? '1' : '0');
    formData.append('price', this.formData.price || 0);
    formData.append('short_description', this.formData.short_description);
    formData.append('description', this.formData.description);
    
    // Objectifs et prérequis
    const learningOutcomes = this.formData.learning_outcomes.filter(o => o.trim() !== '');
    const prerequisites = this.formData.prerequisites.filter(p => p.trim() !== '');
    formData.append('learning_outcomes', JSON.stringify(learningOutcomes));
    formData.append('prerequisites', JSON.stringify(prerequisites));
    formData.append('target_audience', this.formData.target_audience || '');
    
    // Action de publication
    formData.append('publish_action', this.formData.publish_action);
    
    // Image de couverture
    if (this.formData.thumbnail) {
        formData.append('thumbnail', this.formData.thumbnail);
    }
    
    // Vidéo de présentation
    if (this.formData.promo_video) {
        formData.append('promo_video', this.formData.promo_video);
    }
    
    // Ressources
    this.formData.resources.forEach((resource, index) => {
        if (resource.file) {
            formData.append(`resources[${index}]`, resource.file);
        }
    });
    
    // Date de programmation
    if (this.formData.publish_action === 'schedule' && this.formData.scheduled_at) {
        formData.append('scheduled_at', this.formData.scheduled_at);
    }
    
    // ✅ Token CSRF
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    try {

         // ✅ Timeout plus long pour les fichiers volumineux
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minutes
        
        // ✅ ENVOI RÉEL AU SERVEUR
        const response = await fetch('{{ route("instructor.courses.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        // Vérifier le type de réponse
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            
            if (data.success) {
                alert('✅ ' + data.message);
                
                // Rediriger vers le cours créé ou la liste
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '{{ route("instructor.courses.index") }}';
                }
            } else {
                if (data.errors) {
                    const messages = Object.values(data.errors).flat().join('\n');
                    alert('❌ Erreurs de validation :\n' + messages);
                } else {
                    alert('❌ ' + (data.message || 'Erreur lors de la création'));
                }
                this.isSubmitting = false;
            }
        } else {
            // Réponse HTML (redirection probable)
            if (response.ok) {
                window.location.href = '{{ route("instructor.courses.index") }}';
            } else {
                const text = await response.text();
                console.error('Réponse HTML:', text.substring(0, 500));
                alert('❌ Erreur serveur. Vérifiez les logs.');
                this.isSubmitting = false;
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('❌ Erreur de connexion. Veuillez réessayer.');
        this.isSubmitting = false;
    }
}
        }
    }
</script>
@endpush

