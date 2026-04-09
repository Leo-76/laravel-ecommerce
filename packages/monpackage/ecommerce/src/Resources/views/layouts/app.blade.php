<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('ecommerce.boutique.nom', 'Boutique'))</title>
    <meta name="description" content="@yield('meta_description', '')">

    {{-- Tailwind CDN (remplacer par votre build en production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#2563eb', dark: '#1d4ed8', light: '#3b82f6' }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

{{-- Barre de navigation --}}
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('ecommerce.home') }}" class="flex items-center gap-2 font-bold text-xl text-primary">
                @if(config('ecommerce.boutique.logo'))
                    <img src="{{ config('ecommerce.boutique.logo') }}" alt="{{ config('ecommerce.boutique.nom') }}" class="h-8">
                @else
                    <span>🛒</span>
                    <span>{{ config('ecommerce.boutique.nom', 'Ma Boutique') }}</span>
                @endif
            </a>

            {{-- Recherche --}}
            <form action="{{ route('ecommerce.produits.recherche') }}" method="GET" class="hidden md:flex flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Rechercher un produit..."
                        class="w-full border border-gray-300 rounded-full px-5 py-2 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>

            {{-- Actions --}}
            <div class="flex items-center gap-4">
                {{-- Wishlist --}}
                @auth
                <a href="{{ route('ecommerce.wishlist.index') }}" class="text-gray-500 hover:text-red-500 transition-colors" title="Wishlist">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </a>
                @endauth

                {{-- Panier --}}
                <a href="{{ route('ecommerce.panier.index') }}"
                   class="relative text-gray-600 hover:text-primary transition-colors"
                   x-data="panierBadge()"
                   title="Panier">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="count > 0"
                          x-text="count"
                          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"></span>
                </a>

                {{-- Compte --}}
                @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-1 text-gray-600 hover:text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('ecommerce.compte.commandes') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mes commandes</a>
                        <a href="{{ route('ecommerce.compte.profil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mon profil</a>
                        @estAdmin
                        <hr class="my-1">
                        <a href="{{ route('ecommerce.admin.dashboard') }}" class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-medium">⚙️ Admin boutique</a>
                        @endestAdmin
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary">Connexion</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Barre catégories --}}
    <div class="border-t border-gray-100 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 flex gap-6 overflow-x-auto py-2 text-sm">
            <a href="{{ route('ecommerce.produits.index') }}" class="whitespace-nowrap text-gray-600 hover:text-primary font-medium">Tous les produits</a>
            <a href="{{ route('ecommerce.produits.nouveautes') }}" class="whitespace-nowrap text-gray-600 hover:text-primary">🆕 Nouveautés</a>
            <a href="{{ route('ecommerce.produits.promotions') }}" class="whitespace-nowrap text-red-600 hover:text-red-700 font-medium">🔥 Promotions</a>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('succes'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="fixed top-20 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 transition-all">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('succes') }}
</div>
@endif

@if(session('erreur'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-20 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('erreur') }}
</div>
@endif

{{-- Contenu --}}
<main class="min-h-screen">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-gray-800 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <div class="font-bold text-white text-lg mb-3">{{ config('ecommerce.boutique.nom') }}</div>
            <p class="text-sm text-gray-400">Votre boutique en ligne de confiance.</p>
        </div>
        <div>
            <div class="font-semibold text-white mb-3">Boutique</div>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('ecommerce.produits.index') }}" class="hover:text-white">Catalogue</a></li>
                <li><a href="{{ route('ecommerce.produits.nouveautes') }}" class="hover:text-white">Nouveautés</a></li>
                <li><a href="{{ route('ecommerce.produits.promotions') }}" class="hover:text-white">Promotions</a></li>
            </ul>
        </div>
        <div>
            <div class="font-semibold text-white mb-3">Mon compte</div>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('ecommerce.compte.commandes') }}" class="hover:text-white">Mes commandes</a></li>
                <li><a href="{{ route('ecommerce.wishlist.index') }}" class="hover:text-white">Ma wishlist</a></li>
                <li><a href="{{ route('ecommerce.compte.profil') }}" class="hover:text-white">Mon profil</a></li>
            </ul>
        </div>
        <div>
            <div class="font-semibold text-white mb-3">Contact</div>
            <ul class="space-y-2 text-sm">
                <li>{{ config('ecommerce.boutique.email') }}</li>
                @if(config('ecommerce.boutique.telephone'))
                <li>{{ config('ecommerce.boutique.telephone') }}</li>
                @endif
            </ul>
        </div>
    </div>
    <div class="border-t border-gray-700 text-center py-4 text-xs text-gray-500">
        © {{ date('Y') }} {{ config('ecommerce.boutique.nom') }} — Propulsé par MonPackage/Ecommerce
    </div>
</footer>

<script>
function panierBadge() {
    return {
        count: 0,
        init() {
            this.charger();
            window.addEventListener('panier-mis-a-jour', () => this.charger());
        },
        async charger() {
            try {
                const r = await fetch('{{ route("ecommerce.panier.mini") }}');
                const d = await r.json();
                this.count = d.nombre_articles ?? 0;
            } catch(e) {}
        }
    }
}
</script>

@stack('scripts')
</body>
</html>
