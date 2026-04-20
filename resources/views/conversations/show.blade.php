@extends('layouts.public')

@section('title', $conversation->title ?? 'Conversation')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $conversation->title ?? 'Conversation' }}</h1>
                    <p class="text-sm text-gray-500">{{ $conversation->participants_count }} participants</p>
                </div>
                <a href="{{ route('chat.show', $conversation) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-comments mr-2"></i>Ouvrir le chat
                </a>
            </div>
            
            <div class="p-6">
                <h3 class="font-medium text-gray-900 mb-3">Participants</h3>
                <div class="space-y-2">
                    @foreach($conversation->participants as $participant)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $participant->user->avatar }}" class="w-8 h-8 rounded-full">
                                <span>{{ $participant->user->name }}</span>
                                @if($participant->role === 'admin')
                                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Admin</span>
                                @endif
                            </div>
                            @can('update', $conversation)
                                <form action="{{ route('conversations.participants.remove', [$conversation, $participant->user]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                        <i class="fas fa-user-minus mr-1"></i>Retirer
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
                
                @can('update', $conversation)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="font-medium text-gray-900 mb-3">Ajouter des participants</h3>
                        <form action="{{ route('conversations.participants.add', $conversation) }}" method="POST" class="flex space-x-3">
                            @csrf
                            <select name="user_ids[]" multiple class="flex-1 rounded-lg border-gray-300">
                                @foreach(\App\Models\User::whereNotIn('id', $conversation->participants->pluck('user_id'))->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Ajouter
                            </button>
                        </form>
                    </div>
                @endcan
                
                <div class="mt-6 flex justify-end space-x-3">
                    <form action="{{ route('conversations.leave', $conversation) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-out-alt mr-2"></i>Quitter
                        </button>
                    </form>
                    @can('delete', $conversation)
                        <form action="{{ route('conversations.destroy', $conversation) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i>Supprimer
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection