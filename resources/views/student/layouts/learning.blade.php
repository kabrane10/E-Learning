<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Apprentissage') - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        
        .learning-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        .curriculum-sidebar {
            width: 380px;
            height: 100vh;
            background: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        
        .content-area {
            flex: 1;
            overflow-y: auto;
            background: #0f172a;
        }
        
        .lesson-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .lesson-item:hover {
            background-color: #f9fafb;
        }
        
        .lesson-item.active {
            background-color: #eef2ff;
            border-left-color: #4f46e5;
        }
        
        .lesson-item.completed {
            opacity: 0.8;
        }
        
        .lesson-item.completed i.fa-check-circle {
            color: #10b981;
        }
        
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            background: #000;
        }
        
        .video-container video,
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        @media (max-width: 1024px) {
            .curriculum-sidebar {
                width: 320px;
                position: fixed;
                left: -320px;
                z-index: 50;
                transition: left 0.3s ease;
            }
            
            .curriculum-sidebar.open {
                left: 0;
            }
            
            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 40;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="learning-layout" x-data="learningLayout()">
        
        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" 
             class="sidebar-overlay lg:hidden" 
             @click="sidebarOpen = false"
             x-transition></div>
        
        {{-- Sidebar Curriculum --}}
        <aside class="curriculum-sidebar" :class="{ 'open': sidebarOpen }">
            @yield('sidebar')
        </aside>
        
        {{-- Zone de contenu --}}
        <main class="content-area">
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
    <script>
        function learningLayout() {
            return {
                sidebarOpen: false,
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            }
        }
    </script>
</body>
</html>