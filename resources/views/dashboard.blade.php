@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ auth()->user()->name }} 👋</h1>
            <p class="text-gray-400 text-sm mt-0.5">{{ now()->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('ecommerce.produits.index') }}"
           class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
            Voir la boutique →
        </a>
    </div>

    {{-- Stats admin --}}
    @if($statsAdmin)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
            <p class="text-xs font-medium text-blue-600 uppercase tracking-wide mb-1">Commandes aujourd'hui</p>
            <p class="text-3xl font-bold text-blue-800">{{ $statsAdmin['commandes_today'] }}</p>
        </div>
        <div class="bg-green-50 border border-green-100 rounded-2xl p-5">
            <p class="text-xs font-medium text-green-600 uppercase tracking-wide mb-1">CA du jour</p>
            <p class="text-3xl font-bold text-green-800">@prixFormate($statsAdmin['ca_today'])</p>
        </div>
        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5">
            <p class="text-xs font-medium text-orange-600 uppercase tracking-wide mb-1">Ruptures stock</p>
            <p class="text-3xl font-bold text-orange-800">{{ $statsAdmin['produits_rupture'] }}</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-5">
            <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide mb-1">Avis en attente</p>
            <p class="text-3xl font-bold text-yellow-800">{{ $statsAdmin['avis_en_attente'] }}</p>
        </div>
    </div>
    <div class="flex gap-3 mb-8">
        <a href="{{ route('ecommerce.admin.dashboard') }}"
           class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-50">
            🛒 Panel boutique
        </a>
        <a href="{{ route('admin.utilisateurs') }}"
           class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-50">
            👥 Utilisateurs
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Mes commandes --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Mes commandes</h2>
                <a href="{{ route('ecommerce.compte.commandes') }}" class="text-sm text-blue-600 hover:underline">Tout voir →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($mesCommandes as $commande)
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <a href="{{ route('ecommerce.compte.commande', $commande->reference) }}"
                           class="font-mono text-sm font-semibold text-blue-600 hover:underline">
                            {{ $commande->reference }}
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $commande->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800 text-sm">@prixFormate($commande->total)</p>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $commande->statut === 'livree' ? 'bg-green-100 text-green-700' :
                               ($commande->statut === 'expediee' ? 'bg-blue-100 text-blue-700' :
                               ($commande->statut === 'annulee' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $commande->statut_libelle }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-4xl mb-3">🛒</p>
                    <p class="text-sm text-gray-400">Aucune commande pour l'instant.</p>
                    <a href="{{ route('ecommerce.produits.index') }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">
                        Découvrir la boutique →
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Mon espace</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total dépensé</span>
                        <span class="font-semibold">@prixFormate($totalDepense)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">En cours</span>
                        <span class="font-semibold {{ $commandesEnCours > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                            {{ $commandesEnCours }}
                        </span>
                    </div>
                </div>
            </div>

            @if($produitsVedette->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Produits à la une</h3>
                @foreach($produitsVedette as $p)
                <a href="{{ route('ecommerce.produits.show', $p->slug) }}"
                   class="flex items-center gap-3 py-2 hover:bg-gray-50 rounded-lg px-1.5 -mx-1.5 transition-colors">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-sm font-bold text-gray-500 shrink-0">
                        {{ mb_strtoupper(mb_substr($p->nom, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $p->nom }}</p>
                        <p class="text-xs text-gray-400">@prixFormate($p->prix_effectif)</p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
