@extends('layouts.public')

@section('title', 'Contact')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Contactez-nous</h1>
            <p class="text-gray-500 mb-8">Une question ? Nous sommes là pour vous aider.</p>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            
            <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                        <input type="text" name="name" id="name" required
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               value="{{ old('name', auth()->user()->name ?? '') }}">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" required
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               value="{{ old('email', auth()->user()->email ?? '') }}">
                    </div>
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Sujet</label>
                    <input type="text" name="subject" id="subject" required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('subject') }}">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" id="message" rows="5" required
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('message') }}</textarea>
                </div>
                
                <button type="submit" 
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Envoyer le message
                </button>
            </form>
            
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Autres moyens de contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-indigo-600 text-xl w-8"></i>
                        <span class="text-gray-600">contact@elearn.com</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-indigo-600 text-xl w-8"></i>
                        <span class="text-gray-600">+33 1 23 45 67 89</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-indigo-600 text-xl w-8"></i>
                        <span class="text-gray-600">Paris, France</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection