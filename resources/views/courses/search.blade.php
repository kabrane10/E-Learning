@extends('layouts.public')

@section('title', 'Résultats de recherche pour "' . request('q') . '"')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Résultats pour "{{ request('q') }}"
            </h1>
            <p class="text-gray-600">{{ $courses->total() }} cours trouvés</p>
        </div>
        
        @include('courses.partials.course-list', ['courses' => $courses])
    </div>
</div>
@endsection