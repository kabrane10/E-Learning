@extends('layouts.admin')

@section('title', 'Détail utilisateur - ' . $user->name)

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-500">Utilisateurs</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ $user->name }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="userDetailManager({{ $user->id }})">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header avec actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=64' }}" 
                     class="w-16 h-16 rounded-full border-4 border-white shadow-lg">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-1 z-50">
                        <button @click="sendEmail()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-envelope mr-2"></i>Envoyer un email
                        </button>
                        <button @click="impersonate()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-secret mr-2"></i>Usurper l'identité
                        </button>
                        <button @click="toggleStatus()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-ban mr-2"></i>
                            <span x-text="userStatus === 'active' ? 'Désactiver' : 'Activer'"></span>
                        </button>
                        <hr class="my-1">
                        <button @click="confirmDelete()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations générales -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Profil -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user-circle text-indigo-600 mr-2"></i>Profil
                </h3>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-500">Nom complet</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-500">Email vérifié</dt>
                        <dd class="text-sm font-medium">
                            @if($user->email_verified_at)
                                <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>{{ $user->email_verified_at->format('d/m/Y') }}</span>
                            @else
                                <span class="text-yellow-600"><i class="fas fa-exclamation-circle mr-1"></i>Non vérifié</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-500">Rôle</dt>
                        <dd class="text-sm font-medium">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                           {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' : 
                                              ($role->name === 'instructor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm text-gray-500">Inscrit le</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-500">Dernière mise à jour</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>
            
            <!-- Statistiques -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>Statistiques
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ $stats['enrolled_courses'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 uppercase mt-1">Cours suivis</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $stats['completed_courses'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 uppercase mt-1">Cours terminés</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['taught_courses'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 uppercase mt-1">Cours créés</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-purple-600">{{ round($stats['average_progress'] ?? 0) }}%</p>
                        <p class="text-xs text-gray-500 uppercase mt-1">Progression moyenne</p>
                    </div>
                </div>
                
                <!-- Progression globale -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Progression globale</p>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $stats['average_progress'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Activité récente -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>Activité récente
                </h3>
                <div class="space-y-3">
                    @forelse($user->activities ?? [] as $activity)
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-{{ $activity->action === 'login' ? 'sign-in-alt' : ($activity->action === 'enroll' ? 'book-open' : 'check-circle') }} text-indigo-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">{{ $activity->description ?? 'Activité' }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                            <p class="text-sm">Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Cours suivis (si étudiant) -->
        @if($user->hasRole('student') && $user->enrolledCourses->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-book-open text-indigo-600 mr-2"></i>Cours suivis
                    </h3>
                    <span class="text-sm text-gray-500">{{ $user->enrolledCourses->count() }} cours</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Formateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrit le</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->enrolledCourses as $enrollment)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="{{ $enrollment->thumbnail_url }}" class="w-10 h-10 rounded object-cover mr-3">
                                            <span class="font-medium text-gray-900">{{ $enrollment->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $enrollment->instructor->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $enrollment->pivot->progress_percentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ $enrollment->pivot->progress_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $enrollment->pivot->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if($enrollment->pivot->completed_at)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i>Terminé
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">
                                                En cours
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.courses.show', $enrollment) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Cours créés (si formateur) -->
        @if($user->hasRole('instructor') && $user->taughtCourses->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chalkboard-teacher text-indigo-600 mr-2"></i>Cours créés
                    </h3>
                    <span class="text-sm text-gray-500">{{ $user->taughtCourses->count() }} cours</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->taughtCourses as $course)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="{{ $course->thumbnail_url }}" class="w-10 h-10 rounded object-cover mr-3">
                                            <span class="font-medium text-gray-900">{{ $course->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $course->category }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $course->students_count ?? 0 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="text-yellow-400 mr-1">★</span>
                                            <span class="text-sm">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($course->is_published)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Publié</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Brouillon</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Quiz tentés -->
        @if(isset($user->quizAttempts) && $user->quizAttempts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-puzzle-piece text-indigo-600 mr-2"></i>Quiz tentés
                    </h3>
                    <span class="text-sm text-gray-500">{{ $user->quizAttempts->count() }} tentatives</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Résultat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->quizAttempts as $attempt)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $attempt->quiz->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $attempt->quiz->lesson->course->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium {{ $attempt->score >= $attempt->quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($attempt->is_passed)
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Réussi</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Échoué</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p class="text-gray-500 mb-6">
                        Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->name }}</strong> ?<br>
                        Cette action est irréversible.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal envoi email -->
    <div x-show="emailModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="emailModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Envoyer un email à {{ $user->name }}</h3>
                </div>
                <form @submit.prevent="sendEmailSubmit">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                   class="w-full rounded-lg border-gray-300 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sujet</label>
                            <input type="text" x-model="emailForm.subject" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea x-model="emailForm.message" rows="5" required
                                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="emailModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function userDetailManager(userId) {
        return {
            userStatus: 'active',
            deleteModalOpen: false,
            emailModalOpen: false,
            emailForm: {
                subject: '',
                message: ''
            },
            
            sendEmail() {
                this.emailForm.subject = '';
                this.emailForm.message = '';
                this.emailModalOpen = true;
            },
            
            sendEmailSubmit() {
                // Logique d'envoi d'email
                alert('Email envoyé à {{ $user->email }}');
                this.emailModalOpen = false;
            },
            
            impersonate() {
                if (confirm('Usurper l\'identité de {{ $user->name }} ?')) {
                    // Logique d'usurpation
                    window.location.href = '{{ route('admin.users.impersonate', $user) }}';
                }
            },
            
            toggleStatus() {
                // Logique de toggle status
                this.userStatus = this.userStatus === 'active' ? 'inactive' : 'active';
                alert('Statut de l\'utilisateur modifié');
            },
            
            confirmDelete() {
                this.deleteModalOpen = true;
            }
        }
    }
</script>
@endpush