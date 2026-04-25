@extends('layouts.public')

@section('title', 'Maintenance - 503')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-600 to-gray-600">
                503
            </h1>
        </div>
        
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-tools text-6xl text-slate-400"></i>
            </div>
        </div>
        
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Site en maintenance</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Notre site est actuellement en maintenance. Nous serons de retour très bientôt !
        </p>
        
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
            <p class="text-amber-800 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Heure estimée de retour : {{ now()->addHours(2)->format('H:i') }}
            </p>
        </div>
        
        <a href="{{ route('welcome') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
            <i class="fas fa-sync-alt mr-2"></i>
            Actualiser
        </a>
    </div>
</div>
@endsection