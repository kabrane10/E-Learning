@extends('layouts.admin')

@section('title', 'Paramètres du chat')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Paramètres du chat</h1>
                <p class="text-gray-500 mt-1">Configurez les options du chat en temps réel</p>
            </div>
            <a href="{{ route('admin.chat.index') }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form action="{{ route('admin.chat.settings.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Longueur maximale des messages
                    </label>
                    <input type="number" name="max_message_length" value="{{ setting('chat_max_message_length', 2000) }}" 
                           min="100" max="5000" class="w-32 rounded-lg border-gray-300">
                    <p class="text-xs text-gray-500 mt-1">Nombre de caractères maximum par message</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="enable_file_upload" value="1" {{ setting('chat_enable_file_upload', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Activer l'envoi de fichiers</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Taille maximale des fichiers (MB)
                    </label>
                    <input type="number" name="max_file_size" value="{{ setting('chat_max_file_size', 10) }}" 
                           min="1" max="50" class="w-32 rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="enable_typing_indicator" value="1" {{ setting('chat_enable_typing_indicator', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Activer l'indicateur "en train d'écrire"</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="enable_read_receipts" value="1" {{ setting('chat_enable_read_receipts', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Activer les accusés de lecture</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rétention des messages (jours)
                    </label>
                    <input type="number" name="message_retention_days" value="{{ setting('chat_message_retention_days', 90) }}" 
                           min="1" max="365" class="w-32 rounded-lg border-gray-300">
                    <p class="text-xs text-gray-500 mt-1">Les messages plus anciens seront automatiquement supprimés</p>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Enregistrer les paramètres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection