@extends('layouts.public')

@section('title', 'Inscription - ' . $course->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10 text-white text-center">
                <i class="fas fa-lock text-4xl mb-3"></i>
                <h1 class="text-2xl font-bold">Cours Payant</h1>
                <p class="text-indigo-100 mt-2">Inscrivez-vous pour accéder au contenu complet</p>
            </div>
            
            <div class="p-8">
                <!-- Infos du cours -->
                <div class="flex items-center gap-4 mb-6">
                    <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=120' }}" 
                         class="w-24 h-16 object-cover rounded-lg">
                    <div>
                        <h3 class="font-bold text-lg">{{ $course->title }}</h3>
                        <p class="text-gray-500 text-sm">{{ $course->short_description }}</p>
                    </div>
                </div>
                
                <!-- Prix -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6 text-center">
                    <p class="text-gray-500 text-sm mb-1">Prix du cours</p>
                    <p class="text-4xl font-bold text-gray-900">{{ number_format($course->price, 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-gray-500 mt-2">Paiement unique - Accès à vie</p>
                </div>
                
                <!-- Ce qui est inclus -->
                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Accès à toutes les leçons vidéo</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Ressources téléchargeables</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Quiz et exercices pratiques</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Certificat de réussite</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Support du formateur</span>
                    </div>
                </div>
                
                <!-- Bouton de paiement -->
                <button type="button" 
                        @click="processPayment"
                        class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                    <i class="fas fa-credit-card mr-2"></i>
                    Payer {{ number_format($course->price, 0, ',', ' ') }} FCFA
                </button>
                
                <p class="text-xs text-gray-500 text-center mt-4">
                    Paiement sécurisé. Vos données sont protégées.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection