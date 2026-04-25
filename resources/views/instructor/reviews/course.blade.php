@extends('layouts.instructor')

@section('title', 'Avis et Commentaires')
@section('page-title', 'Avis et Commentaires')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Avis</li>
    </ol>
</nav>
@endsection

@section('content')
<div>
    @php $stats = ['average' => 4.8, 'total' => 234, '5star' => 156, '4star' => 45]; $reviews = [['student' => 'Sophie Martin', 'course' => 'Développement Web', 'rating' => 5, 'comment' => 'Excellent cours !', 'date' => '2024-04-15'], ['student' => 'Thomas Dubois', 'course' => 'JavaScript', 'rating' => 4, 'comment' => 'Très bien', 'date' => '2024-04-14']]; @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 text-center"><p class="text-5xl font-bold">{{ $stats['average'] }}</p><div class="flex justify-center text-yellow-400 my-2">★★★★★</div><p class="text-gray-500">{{ $stats['total'] }} avis</p></div>
        <div class="lg:col-span-2 bg-white rounded-xl p-6">@for($i=5; $i>=1; $i--)<div class="flex items-center gap-2"><span>{{ $i }} étoiles</span><div class="flex-1 h-2 bg-gray-200 rounded-full"><div class="bg-yellow-400 h-2 rounded-full" style="width:{{ ($stats[$i.'star']/$stats['total'])*100 }}%"></div></div><span>{{ $stats[$i.'star'] }}</span></div>@endfor</div>
    </div>

    <div class="bg-white rounded-xl overflow-hidden"><div class="px-6 py-4 bg-gray-50"><h3 class="font-semibold">Derniers avis</h3></div>
        <div class="divide-y">@foreach($reviews as $review)<div class="p-6"><div class="flex items-start justify-between"><div><div class="flex items-center gap-2"><span class="font-medium">{{ $review['student'] }}</span><span class="text-gray-400">sur</span><span class="text-indigo-600">{{ $review['course'] }}</span></div><div class="flex text-yellow-400 my-1">@for($i=1;$i<=5;$i++)<i class="fas fa-star{{ $i <= $review['rating'] ? '' : '-empty' }}"></i>@endfor</div><p class="text-gray-700">{{ $review['comment'] }}</p></div><span class="text-sm text-gray-400">{{ $review['date'] }}</span></div></div>@endforeach</div>
    </div>
</div>
@endsection