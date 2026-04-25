<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    [x-cloak] { 
        display: none !important; 
    }
</style>
    <title>@yield('title', 'Messages') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    <style>
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        
        .message-bubble.sent {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 18px 18px 4px 18px;
        }
        
        .message-bubble.received {
            background: #f3f4f6;
            color: #1f2937;
            border-radius: 18px 18px 18px 4px;
        }
        
        .conversation-item {
            transition: all 0.2s ease;
        }
        
        .conversation-item:hover {
            background-color: #f9fafb;
        }
        
        .conversation-item.active {
            background-color: #e0e7ff;
            border-left: 3px solid #4f46e5;
        }
        
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
        }
        
        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #9ca3af;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
        
        .messages-container {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 h-screen overflow-hidden">
    @yield('content')
    
    @stack('scripts')
</body>
</html>