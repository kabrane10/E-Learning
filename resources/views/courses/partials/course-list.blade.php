@if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($courses as $course)
            @include('courses.partials.course-card', ['course' => $course])
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $courses->links() }}
    </div>
@else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-graduation-cap text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun cours trouvé</h3>
        <p class="text-gray-500">Essayez d'ajuster vos critères de recherche</p>
    </div>
@endif