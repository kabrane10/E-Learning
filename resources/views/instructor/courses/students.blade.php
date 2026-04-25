@extends('layouts.instructor')

@section('title', 'Étudiants - ' . $course->title)
@section('page-title', 'Étudiants inscrits')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-500">Mes Cours</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.show', $course) }}" class="text-gray-400 hover:text-gray-500">{{ Str::limit($course->title, 30) }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Étudiants</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .progress-bar {
        transition: width 0.3s ease;
    }
    
    .student-row:hover {
        background-color: #f9fafb;
    }
    
    .completion-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-weight: 500;
    }
    
    .completion-badge.completed {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .completion-badge.in-progress {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .completion-badge.not-started {
        background-color: #f3f4f6;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
<div x-data="studentsManager()">
    
    <!-- En-tête avec stats -->
    <div class="mb-6">
        <a href="{{ route('instructor.courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-700 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i>Retour au cours
        </a>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total étudiants</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $students->total() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">En cours</p>
                        <p class="text-2xl font-bold text-amber-600">{{ $stats['active_students'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-spinner text-amber-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Ont terminé</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['completed_students'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Progression moyenne</p>
                        <p class="text-2xl font-bold text-blue-600">{{ round($stats['average_progress'] ?? 0) }}%</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('instructor.courses.students', $course) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Rechercher un étudiant par nom ou email..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <select name="status" class="w-full py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Non commencé</option>
                </select>
            </div>
            
            <div>
                <select name="sort" class="w-full py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                    <option value="progress_desc" {{ request('sort') == 'progress_desc' ? 'selected' : '' }}>Progression (élevée)</option>
                    <option value="progress_asc" {{ request('sort') == 'progress_asc' ? 'selected' : '' }}>Progression (faible)</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                </select>
            </div>
            
            <div class="md:col-span-4 flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('instructor.courses.students', $course) }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des étudiants -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-user-graduate mr-2 text-indigo-600"></i>
                {{ $students->total() }} étudiant(s) inscrit(s)
            </h3>
            
            <!-- Export -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                    <i class="fas fa-download mr-2"></i>Exporter
                </button>
                <div x-show="open" @click.away="open = false" x-transition x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50">
                    <a href="{{ route('instructor.courses.students', [$course, 'export' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-file-csv mr-2"></i>Exporter en CSV
                    </a>
                    <a href="{{ route('instructor.courses.students', [$course, 'export' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-file-excel mr-2"></i>Exporter en Excel
                    </a>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrit le</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière activité</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr class="student-row transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $student->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=4f46e5&color=fff' }}" 
                                             class="w-10 h-10 rounded-full object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="progress-bar bg-indigo-600 h-2 rounded-full" 
                                             style="width: {{ $student->pivot->progress_percentage ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $student->pivot->progress_percentage ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $progress = $student->pivot->progress_percentage ?? 0;
                                    $isCompleted = $student->pivot->completed_at !== null;
                                @endphp
                                
                                @if($isCompleted)
                                    <span class="completion-badge completed">
                                        <i class="fas fa-check-circle mr-1"></i>Terminé
                                    </span>
                                @elseif($progress > 0)
                                    <span class="completion-badge in-progress">
                                        <i class="fas fa-spinner mr-1"></i>En cours
                                    </span>
                                @else
                                    <span class="completion-badge not-started">
                                        <i class="fas fa-circle mr-1"></i>Non commencé
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <i class="far fa-calendar mr-1 text-gray-400"></i>
                                {{ $student->pivot->enrolled_at ? $student->pivot->enrolled_at->format('d/m/Y') : $student->pivot->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <i class="far fa-clock mr-1 text-gray-400"></i>
                                {{ $student->last_activity_at ? $student->last_activity_at->diffForHumans() : 'Jamais' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('chat.show', ['conversation' => $student->id]) }}" 
                                       class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50"
                                       title="Envoyer un message">
                                        <i class="fas fa-comment-dots"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.users.show', $student) }}" 
                                       target="_blank"
                                       class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50"
                                       title="Voir le profil">
                                        <i class="fas fa-user"></i>
                                    </a>
                                    
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50">
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-chart-simple mr-2"></i>Voir l'activité
                                            </a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-certificate mr-2"></i>Certificat
                                            </a>
                                            <hr class="my-1">
                                            <button @click="confirmRemoveStudent({{ $student->id }})" 
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-user-minus mr-2"></i>Retirer du cours
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-700">Aucun étudiant inscrit</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    @if(request()->has('search') || request()->has('status'))
                                        Aucun étudiant ne correspond à vos critères de recherche.
                                    @else
                                        Les étudiants qui s'inscriront à ce cours apparaîtront ici.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($students->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $students->withQueryString()->links() }}
            </div>
        @endif
    </div>
    
    <!-- Modal de confirmation de retrait -->
    <div x-show="removeModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="removeModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-minus text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Retirer l'étudiant</h3>
                    <p class="text-gray-500 mb-6">
                        Êtes-vous sûr de vouloir retirer cet étudiant du cours ?<br>
                        <span class="text-red-600 font-medium">Sa progression sera perdue.</span>
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="removeModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <form :action="'/instructor/courses/{{ $course->id }}/students/' + studentToRemove" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Retirer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function studentsManager() {
        return {
            removeModalOpen: false,
            studentToRemove: null,
            
            confirmRemoveStudent(studentId) {
                this.studentToRemove = studentId;
                this.removeModalOpen = true;
            }
        }
    }
</script>
@endpush