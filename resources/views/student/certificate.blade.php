@extends('layouts.public')

@section('title', 'Certificat - ' . $course->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-100 to-purple-100 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border-8 border-indigo-200">
            <div class="relative">
                <!-- Bordure décorative -->
                <div class="absolute inset-0 border-8 border-indigo-100 m-4 pointer-events-none"></div>
                
                <div class="p-12 text-center">
                    <!-- En-tête -->
                    <div class="mb-8">
                        <div class="text-6xl mb-4">🎓</div>
                        <h1 class="text-4xl font-serif text-indigo-900 mb-2">Certificat de Réussite</h1>
                        <div class="w-32 h-1 bg-indigo-600 mx-auto"></div>
                    </div>
                    
                    <!-- Présenté à -->
                    <p class="text-gray-600 mb-4">Ce certificat est décerné à</p>
                    
                    <!-- Nom de l'étudiant -->
                    <h2 class="text-5xl font-bold text-gray-900 mb-4 font-serif">{{ $user->name }}</h2>
                    
                    <!-- Pour avoir complété -->
                    <p class="text-gray-600 mb-4">pour avoir complété avec succès le cours</p>
                    
                    <!-- Nom du cours -->
                    <h3 class="text-3xl font-bold text-indigo-700 mb-8">{{ $course->title }}</h3>
                    
                    <!-- Détails -->
                    <div class="grid grid-cols-3 gap-8 mb-8">
                        <div>
                            <p class="text-sm text-gray-500">Date de complétion</p>
                            <p class="text-lg font-semibold">{{ $enrollment->completed_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Durée du cours</p>
                            <p class="text-lg font-semibold">{{ floor($course->lessons->sum('duration') / 60) }} heures</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Formateur</p>
                            <p class="text-lg font-semibold">{{ $course->instructor->name }}</p>
                        </div>
                    </div>
                    
                    <!-- Signature -->
                    <div class="flex items-center justify-center space-x-16 mt-12">
                        <div class="text-center">
                            <div class="w-32 h-px bg-gray-400 mb-2"></div>
                            <p class="text-sm text-gray-600">{{ $course->instructor->name }}</p>
                            <p class="text-xs text-gray-500">Formateur</p>
                        </div>
                        <div class="text-center">
                            <div class="w-32 h-px bg-gray-400 mb-2"></div>
                            <p class="text-sm text-gray-600">E-Learn Platform</p>
                            <p class="text-xs text-gray-500">Directeur</p>
                        </div>
                    </div>
                    
                    <!-- ID unique -->
                    <p class="text-xs text-gray-400 mt-8">
                        ID du certificat : CERT-{{ strtoupper(Str::random(16)) }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-center space-x-4 mt-8">
            <a href="{{ route('student.certificate.download', $course) }}" 
               class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-download mr-2"></i>Télécharger le PDF
            </a>
            <a href="{{ route('student.my-courses') }}" 
               class="px-6 py-3 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Mes cours
            </a>
        </div>
    </div>
</div>
@endsection