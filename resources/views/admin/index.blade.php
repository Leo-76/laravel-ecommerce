@extends('layouts.app')
@section('title', 'Administration')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Administration</h1>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Utilisateurs</p>
            <p class="text-3xl font-bold">{{ $stats['users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">dont {{ $stats['admins'] }} admin(s)</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Commandes</p>
            <p class="text-3xl font-bold">{{ $stats['commandes_total'] }}</p>
            <p class="text-xs text-orange-500 mt-1">{{ $stats['commandes_attente'] }} en attente</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">CA total</p>
            <p class="text-3xl font-bold">@prixFormate($stats['ca_total'])</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['produits'] }} produits actifs</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        <a href="{{ route('ecommerce.admin.dashboard') }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:border-orange-200 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-xl mb-4">🛒</div>
            <h3 class="font-bold text-gray-800 group-hover:text-orange-700 mb-1">Panel Boutique</h3>
            <p class="text-sm text-gray-500">Produits, commandes, coupons, avis.</p>
            <p class="text-xs text-orange-500 mt-3 font-medium">Ouvrir →</p>
        </a>
        <a href="{{ route('admin.utilisateurs') }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:border-purple-200 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-xl mb-4">👥</div>
            <h3 class="font-bold text-gray-800 group-hover:text-purple-700 mb-1">Utilisateurs</h3>
            <p class="text-sm text-gray-500">Gérer les rôles et accès admin boutique.</p>
            <p class="text-xs text-purple-500 mt-3 font-medium">Gérer →</p>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Dernières commandes</h2>
            <a href="{{ route('ecommerce.admin.commandes.index') }}" class="text-sm text-blue-600 hover:underline">Tout voir →</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Référence</th>
                    <th class="px-6 py-3 text-left">Client</th>
                    <th class="px-6 py-3 text-right">Total</th>
                    <th class="px-6 py-3 text-center">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($dernieresCommandes as $c)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3">
                        <a href="{{ route('ecommerce.admin.commandes.show', $c->reference) }}"
                           class="font-mono text-xs text-blue-600 hover:underline font-semibold">{{ $c->reference }}</a>
                    </td>
                    <td class="px-6 py-3 text-gray-500 text-xs">
                        {{ ($c->adresse_livraison['prenom'] ?? '').' '.($c->adresse_livraison['nom'] ?? '') }}
                    </td>
                    <td class="px-6 py-3 text-right font-semibold">@prixFormate($c->total)</td>
                    <td class="px-6 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $c->statut === 'livree' ? 'bg-green-100 text-green-700' :
                               ($c->statut === 'expediee' ? 'bg-blue-100 text-blue-700' :
                               ($c->statut === 'annulee' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $c->statut_libelle }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Aucune commande</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
