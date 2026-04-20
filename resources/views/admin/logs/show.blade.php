@extends('layouts.admin')

@section('title', 'Détail du log')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.logs') }}" class="text-gray-400 hover:text-gray-500">Logs</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ $filename }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $filename }}</h1>
                <p class="text-gray-500 mt-1">{{ count($logs) }} entrées</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.logs.download', ['file' => $date . '/' . $filename]) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-download mr-2"></i>Télécharger
                </a>
                <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
        
        <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
            <div class="divide-y divide-gray-800 max-h-[600px] overflow-y-auto font-mono text-sm">
                @forelse($logs as $log)
                    <div class="px-4 py-3 hover:bg-gray-800 transition-colors">
                        <div class="flex items-start">
                            <span class="text-gray-500 w-36 flex-shrink-0 text-xs">{{ $log['timestamp'] }}</span>
                            <span class="w-20 flex-shrink-0 text-xs font-medium
                                       {{ $log['level'] === 'ERROR' ? 'text-red-400' : 
                                          ($log['level'] === 'WARNING' ? 'text-yellow-400' : 
                                          ($log['level'] === 'INFO' ? 'text-blue-400' : 'text-gray-400')) }}">
                                {{ $log['level'] }}
                            </span>
                            <span class="text-gray-300 break-all flex-1 text-xs">{{ $log['message'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-16 text-center">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                        <p class="text-gray-400">Fichier vide</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection