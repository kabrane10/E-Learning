@extends('layouts.public')

@section('title', 'Conversations')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Mes conversations</h1>
                <a href="{{ route('conversations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Nouvelle
                </a>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($conversations as $conversation)
                    <a href="{{ route('conversations.show', $conversation) }}" class="block p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $conversation->title ?? 'Conversation #' . $conversation->id }}</h3>
                                <p class="text-sm text-gray-500">{{ $conversation->participants_count }} participants</p>
                            </div>
                            <span class="text-sm text-gray-400">{{ $conversation->updated_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-center text-gray-500">Aucune conversation</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection