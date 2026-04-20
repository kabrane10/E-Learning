@extends('layouts.public')

@section('title', 'Mon apprentissage')

@section('content')
<div class="bg-gradient-to-br from-indigo-600 to-purple-700">
    <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white">Mon apprentissage</h1>
        <p class="mt-2 text-indigo-100">Continuez à progresser vers vos objectifs</p>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-2 gap-4 mt-8 md:grid-cols-5">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-indigo-100 text-sm">Cours suivis</div>
                <div class="text-3xl font-bold text-white mt-1">{{ $totalCourses }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-indigo-100 text-sm">Terminés</div>
                <div class="text-3xl font-bold text-white mt-1">{{ $completedCourses }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-indigo-100 text-sm">Heures</div>
                <div class="text-3xl font-bold text-white mt-1">{{ round($totalHours) }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-indigo-100 text-sm">Niveau</div>
                <div class="text-3xl font-bold text-white mt-1">{{ auth()->user()->current_level ?? 1 }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-indigo-100 text-sm">Points</div>
                <div class="text-3xl font-bold text-white mt-1">{{ number_format(auth()->user()->total_points ?? 0) }}</div>
            </div>
        </div>
        
        <!-- Progression de niveau -->
        @php
            $userLevel = auth()->user()->current_level ?? 1;
            $currentLevel = \App\Models\Level::where('level_number', $userLevel)->first() ?? \App\Models\Level::first();
            $nextLevel = \App\Models\Level::where('level_number', '>', $userLevel)->orderBy('level_number')->first();
            $userPoints = auth()->user()->total_points ?? 0;
        @endphp
        
        @if($nextLevel && $currentLevel)
            <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="flex items-center justify-between text-white text-sm mb-2">
                    <span>
                        <span class="mr-2">{{ $currentLevel->icon }}</span>
                        Niveau {{ $userLevel }} - {{ $currentLevel->name }}
                    </span>
                    <span>{{ number_format($userPoints) }} / {{ number_format($nextLevel->points_required) }} XP</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 h-2.5 rounded-full transition-all duration-500" 
                         style="width: {{ $currentLevel->getProgressToNextLevel($userPoints) }}%"></div>
                </div>
                <p class="text-white/70 text-xs mt-2">
                    <i class="fas fa-arrow-up mr-1"></i>
                    Plus que {{ number_format($nextLevel->points_required - $userPoints) }} XP pour atteindre le niveau {{ $nextLevel->level_number }} - {{ $nextLevel->name }} {{ $nextLevel->icon }}
                </p>
            </div>
        @endif
        
        <!-- Série -->
        @if(auth()->user()->streak_days > 0)
            <div class="mt-4 flex items-center text-white/90 text-sm">
                <i class="fas fa-fire text-orange-400 mr-2"></i>
                <span>{{ auth()->user()->streak_days }} jour{{ auth()->user()->streak_days > 1 ? 's' : '' }} de série ! Continuez comme ça !</span>
            </div>
        @endif
    </div>
</div>

<div class="py-12 bg-gray-50">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Cours en cours -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Continuer l'apprentissage</h2>
        
        @if($enrolledCourses->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mb-12">
                @foreach($enrolledCourses as $course)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <a href="{{ route('student.learn', $course) }}">
                            <img src="{{ $course->thumbnail_url }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-40 object-cover">
                        </a>
                        <div class="p-5">
                            <a href="{{ route('student.learn', $course) }}" class="block">
                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-1">{{ $course->title }}</h3>
                                <p class="text-sm text-gray-500 mb-3">{{ $course->instructor->name }}</p>
                            </a>
                            
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progression</span>
                                    <span>{{ $course->pivot->progress_percentage }}%</span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full transition-all duration-300" 
                                         style="width: {{ $course->pivot->progress_percentage }}%"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                @if($course->pivot->completed_at)
                                    <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Terminé
                                    </span>
                                @else
                                    <a href="{{ route('student.learn', $course) }}" 
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                        Continuer <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl mb-12">
                <i class="fas fa-book-open text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun cours suivi</h3>
                <p class="text-gray-500 mb-6">Commencez votre apprentissage en explorant notre catalogue</p>
                <a href="{{ route('courses.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Explorer les cours
                </a>
            </div>
        @endif
        
        <!-- Section Gamification & Forum -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Badges récents -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-medal text-indigo-600 mr-2"></i>Badges récents
                    </h3>
                    <a href="{{ route('gamification.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                @php
                    $recentBadges = auth()->user()->badges()
                        ->whereNotNull('user_badges.earned_at')
                        ->latest('user_badges.earned_at')
                        ->limit(4)
                        ->get();
                @endphp
                
                @if($recentBadges->count() > 0)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($recentBadges as $badge)
                            <div class="text-center group cursor-help" title="{{ $badge->description }}">
                                <div class="w-14 h-14 mx-auto rounded-full bg-gradient-to-br from-{{ $badge->color }}-400 to-{{ $badge->color }}-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                    <span class="text-2xl text-white">{{ $badge->icon }}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $badge->name }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-4">
                        Continuez à apprendre pour gagner des badges !
                    </p>
                @endif
            </div>
            
            <!-- Activité forum -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-comments text-indigo-600 mr-2"></i>Forum
                    </h3>
                    <a href="{{ route('forum.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Participer <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                @php
                    $userTopics = \App\Models\ForumTopic::where('user_id', auth()->id())
                        ->latest()
                        ->limit(3)
                        ->get();
                @endphp
                
                @if($userTopics->count() > 0)
                    <div class="space-y-3">
                        @foreach($userTopics as $topic)
                            <a href="{{ $topic->url }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <p class="font-medium text-gray-900 text-sm line-clamp-1">{{ $topic->title }}</p>
                                <div class="flex items-center text-xs text-gray-500 mt-1 space-x-3">
                                    <span><i class="far fa-comment mr-1"></i>{{ $topic->posts_count }} réponses</span>
                                    <span><i class="far fa-eye mr-1"></i>{{ $topic->views_count }} vues</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-4">
                        <a href="{{ route('forum.topics.create') }}" class="text-indigo-600 hover:text-indigo-700">
                            Créez votre premier sujet
                        </a> sur le forum !
                    </p>
                @endif
            </div>
            
            <!-- Classement -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-trophy text-indigo-600 mr-2"></i>Classement
                    </h3>
                    <a href="{{ route('gamification.leaderboard') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="text-center">
                    @php
                        $rank = \App\Models\User::where('total_points', '>', auth()->user()->total_points ?? 0)->count() + 1;
                    @endphp
                    <p class="text-5xl font-bold text-indigo-600">#{{ $rank }}</p>
                    <p class="text-gray-500 text-sm mt-1">Votre position</p>
                    
                    @if(auth()->user()->streak_days > 0)
                        <div class="mt-4 inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded-full">
                            <i class="fas fa-fire mr-2"></i>
                            <span class="font-medium">{{ auth()->user()->streak_days }} jours de série !</span>
                        </div>
                    @endif
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-users mr-1"></i>
                            {{ \App\Models\User::count() }} participants au classement
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recommandations -->
        @if($recommendedCourses->count() > 0)
            <h2 class="text-2xl font-bold text-gray-900 mt-12 mb-6">Recommandé pour vous</h2>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($recommendedCourses as $course)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                        <a href="{{ route('courses.show', $course) }}">
                            <div class="relative">
                                <img src="{{ $course->thumbnail_url }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4">
                                <h4 class="font-medium text-gray-900 line-clamp-2 text-sm mb-1">
                                    {{ $course->title }}
                                </h4>
                                <p class="text-xs text-gray-500 mb-2">{{ $course->instructor->name }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-indigo-600 font-medium">Gratuit</span>
                                    <span class="text-xs text-gray-400">{{ $course->lessons_count }} leçons</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection