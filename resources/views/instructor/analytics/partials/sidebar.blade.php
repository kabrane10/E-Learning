<aside class="instructor-sidebar">
    <div class="flex items-center justify-center h-16 px-4 bg-[#1e1b4b] border-b border-indigo-800/30">
        <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chalkboard-teacher text-white text-lg"></i>
            </div>
            <span class="text-white font-bold text-xl">Espace Formateur</span>
        </a>
    </div>
    <nav class="flex-1 px-4 py-6 overflow-y-auto">
        <a href="{{ route('instructor.dashboard') }}" class="sidebar-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i>Tableau de bord</a>
        <div class="sidebar-section-title"><i class="fas fa-book-open mr-1"></i> Mes Cours</div>
        <a href="{{ route('instructor.courses.index') }}" class="sidebar-link {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}"><i class="fas fa-list"></i>Tous mes cours</a>
        <a href="{{ route('instructor.courses.create') }}" class="sidebar-link {{ request()->routeIs('instructor.courses.create') ? 'active' : '' }}"><i class="fas fa-plus-circle"></i>Créer un cours</a>
        <div class="sidebar-section-title"><i class="fas fa-puzzle-piece mr-1"></i> Quiz</div>
        <a href="{{ route('instructor.quizzes.index') }}" class="sidebar-link {{ request()->routeIs('instructor.quizzes.*') ? 'active' : '' }}"><i class="fas fa-puzzle-piece"></i>Mes quiz</a>
        <div class="sidebar-section-title"><i class="fas fa-chart-bar mr-1"></i> Analyses</div>
        <a href="{{ route('instructor.analytics') }}" class="sidebar-link {{ request()->routeIs('instructor.analytics') ? 'active' : '' }}"><i class="fas fa-chart-line"></i>Vue d'ensemble</a>
        <a href="{{ route('instructor.earnings') }}" class="sidebar-link {{ request()->routeIs('instructor.earnings') ? 'active' : '' }}"><i class="fas fa-euro-sign"></i>Revenus</a>
        <div class="sidebar-section-title"><i class="fas fa-users mr-1"></i> Communauté</div>
        <a href="{{ route('instructor.reviews.index') }}" class="sidebar-link"><i class="fas fa-star"></i>Avis</a>
        <a href="{{ route('chat.index') }}" class="sidebar-link"><i class="fas fa-comment-dots"></i>Messages</a>
        <div class="sidebar-section-title"><i class="fas fa-cog mr-1"></i> Paramètres</div>
        <a href="{{ route('instructor.profile.edit') }}" class="sidebar-link"><i class="fas fa-user-circle"></i>Mon profil</a>
        <a href="{{ route('instructor.profile.settings') }}" class="sidebar-link"><i class="fas fa-sliders-h"></i>Préférences</a>
        <div class="sidebar-section-title"><i class="fas fa-globe mr-1"></i> Navigation</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link"><i class="fas fa-user-graduate"></i>Espace étudiant</a>
        <a href="{{ route('home') }}" class="sidebar-link"><i class="fas fa-external-link-alt"></i>Voir le site</a>
    </nav>
    <div class="p-4 border-t border-indigo-800/30 flex-shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" class="w-10 h-10 rounded-full border-2 border-indigo-500 object-cover">
            <div class="flex-1 min-w-0"><p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p><p class="text-xs text-indigo-300">Formateur</p></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-gray-400 hover:text-white transition-colors" title="Déconnexion"><i class="fas fa-sign-out-alt"></i></button></form>
        </div>
    </div>
</aside>