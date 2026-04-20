@extends('layouts.public')

@section('title', 'Mes favoris')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-bookmark text-indigo-600 mr-3"></i>Mes favoris
            </h1>
            <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-plus mr-2"></i>Explorer les cours
            </a>
        </div>
        
        @if($bookmarks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($bookmarks as $course)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow overflow-hidden">
                        <a href="{{ route('courses.show', $course) }}">
                            <img src="{{ $course->thumbnail_url }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-48 object-cover">
                        </a>
                        
                        <div class="p-6">
                            <a href="{{ route('courses.show', $course) }}" class="block">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-indigo-600">
                                    {{ $course->title }}
                                </h3>
                            </a>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $course->short_description }}
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $course->instructor->avatar }}" 
                                         class="w-8 h-8 rounded-full mr-2">
                                    <span class="text-sm text-gray-600">{{ $course->instructor->name }}</span>
                                </div>
                                
                                <button class="remove-bookmark text-red-500 hover:text-red-700"
                                        data-course-id="{{ $course->id }}">
                                    <i class="fas fa-trash mr-1"></i>Retirer
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $bookmarks->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <i class="far fa-bookmark text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun favori</h3>
                <p class="text-gray-500 mb-6">Ajoutez des cours à vos favoris pour les retrouver facilement</p>
                <a href="{{ route('courses.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-compass mr-2"></i>Explorer les cours
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.remove-bookmark').forEach(btn => {
        btn.addEventListener('click', async function() {
            const courseId = this.dataset.courseId;
            
            if (confirm('Retirer ce cours de vos favoris ?')) {
                try {
                    const response = await fetch(`/student/bookmark/${courseId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        this.closest('.bg-white').remove();
                        
                        // Si plus de cours, recharger la page
                        if (document.querySelectorAll('.bg-white').length === 0) {
                            window.location.reload();
                        }
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            }
        });
    });
</script>
@endpush