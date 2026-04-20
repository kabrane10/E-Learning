<div class="relative" x-data="{ open: false }">
    <!-- Icône de notification -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-indigo-600 focus:outline-none">
        <i class="far fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>
    
    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 z-50 max-h-[80vh] overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-sm text-indigo-600 hover:text-indigo-700">
                    Tout marquer comme lu
                </button>
            @endif
        </div>
        
        <div class="overflow-y-auto max-h-96">
            @forelse($notifications as $notification)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-indigo-50' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-2xl mr-3">
                            {{ $notification->data['icon'] ?? '📢' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 mb-1">
                                {{ $notification->data['message'] }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}" 
                                       class="text-xs text-indigo-600 hover:text-indigo-700">
                                        Voir
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0 ml-3 space-x-1">
                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')" 
                                        class="text-xs text-gray-400 hover:text-indigo-600">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                            <button wire:click="delete('{{ $notification->id }}')" 
                                    class="text-xs text-gray-400 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="far fa-bell-slash text-3xl mb-2"></i>
                    <p>Aucune notification</p>
                </div>
            @endforelse
        </div>
        
        <div class="p-3 border-t border-gray-200 bg-gray-50">
            <a href="{{ route('notifications.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                Voir toutes les notifications
            </a>
        </div>
    </div>
</div>