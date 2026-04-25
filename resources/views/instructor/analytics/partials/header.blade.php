<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="hidden max-lg:block p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg font-semibold text-gray-900 truncate">@yield('page-title', 'Tableau de bord')</h1>
            </div>
            <div class="flex-1 max-w-lg mx-4 search-desktop">
                <form class="relative"><input type="text" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"><i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i></form>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full"><i class="far fa-bell text-lg"></i><span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span></button>
                    <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
                        <div class="p-4 border-b"><h3 class="font-semibold">Notifications</h3></div>
                        <div class="divide-y"><div class="p-4 hover:bg-gray-50"><p class="text-sm">Nouvel étudiant inscrit</p><p class="text-xs text-gray-500">Il y a 2h</p></div></div>
                    </div>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100"><img src="{{ Auth::user()->avatar }}" class="w-8 h-8 rounded-full border object-cover"><span class="text-sm font-medium user-name-desktop">{{ Auth::user()->name }}</span><i class="fas fa-chevron-down text-xs text-gray-400 user-name-desktop"></i></button>
                    <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border z-50"><div class="p-3 border-b"><p class="text-sm font-medium">{{ Auth::user()->name }}</p><p class="text-xs text-gray-500">{{ Auth::user()->email }}</p></div><div class="py-1"><a href="{{ route('instructor.profile.edit') }}" class="flex items-center px-4 py-2 text-sm hover:bg-gray-50"><i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>Mon profil</a><hr class="my-1"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center"><i class="fas fa-sign-out-alt w-5 mr-3"></i>Déconnexion</button></form></div></div>
                </div>
            </div>
        </div>
    </div>
</header>