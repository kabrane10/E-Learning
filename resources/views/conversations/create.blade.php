@extends('layouts.public')

@section('title', 'Nouvelle conversation')

@section('content')
<div class="py-8">
    <div class="max-w-lg mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Nouvelle conversation</h1>
            </div>
            
            <form action="{{ route('conversations.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="type" class="w-full rounded-lg border-gray-300" required>
                        <option value="private">Message privé</option>
                        <option value="course">Discussion de cours</option>
                        <option value="group">Groupe</option>
                    </select>
                </div>
                
                <div id="user-select">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
                    <select name="user_id" class="w-full rounded-lg border-gray-300">
                        <option value="">Sélectionner un utilisateur</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div id="course-select" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cours</label>
                    <select name="course_id" class="w-full rounded-lg border-gray-300">
                        <option value="">Sélectionner un cours</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div id="title-input" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom du groupe</label>
                    <input type="text" name="title" class="w-full rounded-lg border-gray-300" placeholder="Ex: Groupe de projet">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('chat.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function() {
        document.getElementById('user-select').style.display = this.value === 'private' ? 'block' : 'none';
        document.getElementById('course-select').style.display = this.value === 'course' ? 'block' : 'none';
        document.getElementById('title-input').style.display = this.value === 'group' ? 'block' : 'none';
        
        // Mettre à jour required
        document.querySelector('#user-select select').required = this.value === 'private';
        document.querySelector('#course-select select').required = this.value === 'course';
        document.querySelector('#title-input input').required = this.value === 'group';
    });
</script>
@endpush