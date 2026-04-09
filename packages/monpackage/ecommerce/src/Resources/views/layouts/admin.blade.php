<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Tableau de bord') | {{ config('ecommerce.boutique.nom') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col shrink-0">
        <div class="px-6 py-5 border-b border-gray-700">
            <a href="{{ route('ecommerce.admin.dashboard') }}" class="text-white font-bold text-lg flex items-center gap-2">
                <span>🛒</span> Admin Boutique
            </a>
            <p class="text-xs text-gray-500 mt-1">{{ config('ecommerce.boutique.nom') }}</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $current = request()->route()->getName();
                $link = fn($route, $icon, $label) =>
                    '<a href="' . route($route) . '" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm ' .
                    (str_starts_with($current, str_replace('dashboard', '', $route))
                        ? 'bg-blue-600 text-white'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white') .
                    ' transition-colors">' . $icon . ' ' . $label . '</a>';
            @endphp

            {!! $link('ecommerce.admin.dashboard',        '📊', 'Tableau de bord') !!}
            {!! $link('ecommerce.admin.commandes.index',  '📦', 'Commandes') !!}
            {!! $link('ecommerce.admin.produits.index',   '🏷️',  'Produits') !!}
            {!! $link('ecommerce.admin.categories.index', '📂', 'Catégories') !!}
            {!! $link('ecommerce.admin.coupons.index',    '🎫', 'Coupons') !!}
            {!! $link('ecommerce.admin.avis.index',       '⭐', 'Avis clients') !!}
            {!! $link('ecommerce.admin.parametres',       '⚙️',  'Paramètres') !!}

            <div class="pt-4 border-t border-gray-700">
                <a href="{{ route('ecommerce.home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-white transition-colors">
                    🏪 Voir la boutique
                </a>
            </div>
        </nav>

        <div class="px-4 py-3 border-t border-gray-700 text-xs text-gray-500">
            Connecté : {{ auth()->user()->name ?? 'Admin' }}
        </div>
    </aside>

    {{-- Contenu principal --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
            <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Tableau de bord')</h1>
            <div class="flex items-center gap-4">
                @if(session('succes'))
                <span class="text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full">✓ {{ session('succes') }}</span>
                @endif
                @if(session('erreur'))
                <span class="text-sm text-red-600 bg-red-50 px-3 py-1 rounded-full">✗ {{ session('erreur') }}</span>
                @endif
            </div>
        </header>

        {{-- Main scroll area --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
