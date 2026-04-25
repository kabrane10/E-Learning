@props(['activities' => []])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">Activité Récente</h3>
        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">Voir tout</a>
    </div>
    <div class="p-4">
        <div class="space-y-4">
            @forelse($activities as $activity)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">Aucune activité récente</p>
            @endforelse
        </div>
    </div>
</div>