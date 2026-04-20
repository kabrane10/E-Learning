@extends('layouts.public')

@section('title', 'Notifications')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="far fa-bell text-indigo-600 mr-3"></i>Notifications
                </h1>
                
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-700">
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-indigo-50' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-3xl mr-4">
                                {{ $notification->data['icon'] ?? '📢' }}
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 mb-1">
                                    {{ $notification->data['message'] }}
                                </p>
                                
                                <div class="flex items-center text-sm text-gray-500">
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    
                                    @if(isset($notification->data['action_url']))
                                        <span class="mx-2">•</span>
                                        <a href="{{ $notification->data['action_url'] }}" 
                                           class="text-indigo-600 hover:text-indigo-700 font-medium">
                                            Voir
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0 ml-4 flex items-center space-x-2">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-700">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="far fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Aucune notification</h3>
                        <p class="text-gray-500">Vous êtes à jour !</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection