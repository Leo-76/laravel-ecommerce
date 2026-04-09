<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Accueil')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">

        <div class="flex items-center gap-6">
            <a href="{{ route('home') }}" class="font-bold text-gray-900 flex items-center gap-2">
                <span class="text-xl">🛒</span>
                <span class="text-sm">{{ config('app.name') }}</span>
            </a>
            @auth
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}"
                   class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('ecommerce.home') }}"
                   class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('ecommerce.*') && !request()->routeIs('ecommerce.admin.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Boutique
                </a>
                @can('admin')
                <a href="{{ route('admin.index') }}"
                   class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('admin.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Admin
                </a>
                <a href="{{ route('ecommerce.admin.dashboard') }}"
                   class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('ecommerce.admin.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Admin Boutique
                </a>
                @endcan
            </div>
            @endauth
        </div>

        <div class="flex items-center gap-3">
            @auth
            <a href="{{ route('ecommerce.panier.index') }}" class="relative text-gray-500 hover:text-gray-700 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                @if(isset($_panierResume) && $_panierResume['nombre_articles'] > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">
                    {{ $_panierResume['nombre_articles'] }}
                </span>
                @endif
            </a>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700">
                    <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span class="hidden md:block">{{ auth()->user()->name }}</span>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                    <a href="{{ route('ecommerce.compte.commandes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mes commandes</a>
                    <a href="{{ route('ecommerce.wishlist.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ma wishlist</a>
                    <hr class="my-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                Connexion
            </a>
            <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 font-medium">
                S'inscrire
            </a>
            @endauth
        </div>
    </div>
</nav>

@if(session('succes') || session('success'))
<div class="bg-green-50 border-b border-green-100 px-4 py-2.5 text-sm text-green-800 flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('succes') ?? session('success') }}
</div>
@endif
@if(session('erreur') || session('error'))
<div class="bg-red-50 border-b border-red-100 px-4 py-2.5 text-sm text-red-800 flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('erreur') ?? session('error') }}
</div>
@endif

<main>@yield('content')</main>

@stack('scripts')
</body>
</html>
