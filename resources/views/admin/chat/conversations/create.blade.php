@extends('layouts.admin')

@section('title', 'Nouvelle conversation')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.chat.index') }}" class="text-gray-400 hover:text-gray-500">Chat</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.chat.conversations.index') }}" class="text-gray-400 hover:text-gray-500">Conversations</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Nouvelle</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Nouvelle conversation</h2>
            </div>
            
            <form action="{{ route('admin.chat.conversations.store') }}" method="POST" class="p-6 space-y-6" x-data="{ type: 'private' }">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de conversation</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="type === 'private' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="private" x-model="type" class="sr-only">
                            <i class="fas fa-user mr-2" :class="type === 'private' ? 'text-indigo-600' : 'text-gray-400'"></i>
                            <span :class="type === 'private' ? 'text-indigo-600 font-medium' : 'text-gray-700'">Privée</span>
                        </label>
                        <label class="relative flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="type === 'course' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="course" x-model="type" class="sr-only">
                            <i class="fas fa-book mr-2" :class="type === 'course' ? 'text-indigo-600' : 'text-gray-400'"></i>
                            <span :class="type === 'course' ? 'text-indigo-600 font-medium' : 'text-gray-700'">Cours</span>
                        </label>
                        <label class="relative flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                               :class="type === 'group' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="group" x-model="type" class="sr-only">
                            <i class="fas fa-users mr-2" :class="type === 'group' ? 'text-indigo-600' : 'text-gray-400'"></i>
                            <span :class="type === 'group' ? 'text-indigo-600 font-medium' : 'text-gray-700'">Groupe</span>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div x-show="type === 'private'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
                    <select name="user_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un utilisateur</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div x-show="type === 'course'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cours</label>
                    <select name="course_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sélectionner un cours</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div x-show="type === 'group'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom du groupe</label>
                    <input type="text" name="title" value="{{ old('title') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Groupe de projet">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.chat.conversations.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Créer la conversation
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush