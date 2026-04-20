@extends('layouts.public')

@section('title', 'Apprenez sans limites')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="px-4 py-20 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid items-center grid-cols-1 gap-12 lg:grid-cols-2">
            <div>
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Apprenez</span>
                    <span class="block text-indigo-600">sans limites</span>
                </h1>
                <p class="mt-6 text-lg text-gray-500 sm:text-xl max-w-2xl">
                    Accédez à des milliers de cours gratuits créés par des experts. 
                    Développez vos compétences à votre rythme, où que vous soyez.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('courses.index') }}" 
                       class="px-8 py-4 text-base font-medium text-white bg-indigo-600 rounded-lg shadow-lg hover:bg-indigo-700 transform hover:scale-105 transition-all duration-200">
                        Explorer les cours
                    </a>
                    @guest
                    <a href="{{ route('register') }}" 
                       class="px-8 py-4 text-base font-medium text-indigo-600 bg-white border-2 border-indigo-600 rounded-lg hover:bg-indigo-50 transform hover:scale-105 transition-all duration-200">
                        S'inscrire gratuitement
                    </a>
                    @endguest
                </div>
                <div class="mt-8 flex items-center space-x-6">
                    <div class="flex -space-x-2">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=John+Doe" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Jane+Smith" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Mike+Johnson" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Sarah+Williams" alt="">
                    </div>
                    <p class="text-sm text-gray-500">
                        <span class="font-semibold text-gray-900">+10 000</span> apprenants nous font confiance
                    </p>
                </div>
            </div>
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                     alt="Apprentissage en ligne"
                     class="rounded-2xl shadow-2xl">
                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Étudiants actifs</p>
                            <p class="text-2xl font-bold text-gray-900">2,500+</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -top-6 -right-6 bg-white p-4 rounded-xl shadow-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-video text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Cours disponibles</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $coursesCount ?? '150' }}+</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Catégories populaires -->
<div class="py-16 bg-white">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Catégories populaires</h2>
            <p class="mt-4 text-lg text-gray-500">Trouvez le cours qui correspond à vos objectifs</p>
        </div>
        
        <div class="grid grid-cols-2 gap-6 mt-12 md:grid-cols-3 lg:grid-cols-4">
            @foreach(['Développement Web', 'Data Science', 'Design', 'Marketing', 'Business', 'Photographie', 'Musique', 'Langues'] as $category)
                <a href="{{ route('courses.index', ['category' => $category]) }}" 
                   class="group p-6 bg-gray-50 rounded-xl hover:bg-indigo-50 transition-colors duration-200">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                        <i class="fas fa-{{ $category === 'Développement Web' ? 'code' : ($category === 'Data Science' ? 'chart-line' : ($category === 'Design' ? 'paint-brush' : ($category === 'Marketing' ? 'bullhorn' : ($category === 'Business' ? 'briefcase' : ($category === 'Photographie' ? 'camera' : ($category === 'Musique' ? 'music' : 'language')))))) }} text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $category }}</h3>
                    <p class="mt-1 text-sm text-gray-500">12 cours</p>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Cours en vedette -->
<div class="py-16 bg-gray-50">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-gray-900">Cours en vedette</h2>
            <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                Voir tout <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($featuredCourses ?? [] as $course)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden group">
                    <a href="{{ route('courses.show', $course) }}">
                        <div class="relative">
                            <img src="{{ $course->thumbnail_url }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <span class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-sm text-xs font-medium text-gray-900 rounded-full">
                                {{ ucfirst($course->level) }}
                            </span>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-medium">
                                    {{ $course->category }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                {{ $course->title }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                                {{ $course->short_description }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $course->instructor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}" 
                                         class="w-8 h-8 rounded-full mr-2">
                                    <span class="text-sm text-gray-600">{{ $course->instructor->name }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-book-open mr-1"></i>
                                    <span>{{ $course->lessons_count }} leçons</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500">Aucun cours disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="py-16 bg-white">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
            <div class="text-center">
                <div class="text-4xl font-bold text-indigo-600">150+</div>
                <div class="mt-2 text-sm text-gray-500">Cours disponibles</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-indigo-600">10k+</div>
                <div class="mt-2 text-sm text-gray-500">Étudiants</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-indigo-600">50+</div>
                <div class="mt-2 text-sm text-gray-500">Formateurs experts</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-indigo-600">4.8</div>
                <div class="mt-2 text-sm text-gray-500">Note moyenne</div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-indigo-600">
    <div class="px-4 py-16 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-white">Prêt à commencer votre apprentissage ?</h2>
            <p class="mt-4 text-lg text-indigo-100">Rejoignez notre communauté d'apprenants dès aujourd'hui.</p>
            @guest
            <div class="mt-8">
                <a href="{{ route('register') }}" 
                   class="px-8 py-4 text-base font-medium text-indigo-600 bg-white rounded-lg shadow-lg hover:bg-gray-50 transform hover:scale-105 transition-all duration-200 inline-block">
                    Commencer gratuitement
                </a>
            </div>
            @endguest
        </div>
    </div>
</div>
@endsection