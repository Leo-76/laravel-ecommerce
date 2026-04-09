@extends('ecommerce::layouts.admin')
@section('title', 'Tableau de bord')

@section('content')

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @foreach([
        ['label' => 'CA ce mois', 'value' => $stats['ca_mois_formate'], 'icon' => '💰', 'evolution' => $stats['evolution_ca'], 'color' => 'blue'],
        ['label' => 'Commandes totales', 'value' => number_format($stats['commandes_total']), 'icon' => '📦', 'sub' => $stats['commandes_en_attente'] . ' en attente', 'color' => 'purple'],
        ['label' => 'Produits actifs', 'value' => number_format($stats['produits_total']), 'icon' => '🏷️', 'sub' => $stats['produits_rupture'] . ' en rupture', 'color' => 'green'],
        ['label' => 'Panier moyen', 'value' => $stats['panier_moyen'], 'icon' => '🛒', 'sub' => $stats['commandes_mois'] . ' cmd ce mois', 'color' => 'orange'],
    ] as $kpi)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between mb-3">
            <span class="text-2xl">{{ $kpi['icon'] }}</span>
            @if(isset($kpi['evolution']))
            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $kpi['evolution'] >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $kpi['evolution'] >= 0 ? '+' : '' }}{{ $kpi['evolution'] }}%
            </span>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-gray-900">{{ $kpi['value'] }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $kpi['label'] }}</p>
        @if(isset($kpi['sub']))
        <p class="text-xs text-gray-400 mt-0.5">{{ $kpi['sub'] }}</p>
        @endif
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Dernières commandes --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-gray-800">Dernières commandes</h2>
            <a href="{{ route('ecommerce.admin.commandes.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Référence</th>
                        <th class="pb-3">Client</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Statut</th>
                        <th class="pb-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($dernieresCommandes as $commande)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3">
                            <a href="{{ route('ecommerce.admin.commandes.show', $commande->reference) }}"
                               class="font-mono font-semibold text-blue-600 hover:underline text-xs">
                                {{ $commande->reference }}
                            </a>
                        </td>
                        <td class="py-3 text-gray-600">
                            {{ $commande->adresse_livraison['prenom'] ?? $commande->client?->name ?? '—' }}
                            {{ $commande->adresse_livraison['nom'] ?? '' }}
                        </td>
                        <td class="py-3 font-semibold">@prixFormate($commande->total)</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $commande->statut === 'livree' ? 'bg-green-100 text-green-700' :
                                   ($commande->statut === 'annulee' ? 'bg-red-100 text-red-700' :
                                   ($commande->statut === 'expediee' ? 'bg-blue-100 text-blue-700' :
                                   'bg-yellow-100 text-yellow-700')) }}">
                                {{ $commande->statut_libelle }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-400 text-xs">{{ $commande->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sidebar alertes --}}
    <div class="space-y-5">

        {{-- Stock faible --}}
        @if($produitsFaibleStock->isNotEmpty())
        <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                ⚠️ <span>Stock faible</span>
            </h3>
            <div class="space-y-3">
                @foreach($produitsFaibleStock as $produit)
                <div class="flex items-center justify-between">
                    <a href="{{ route('ecommerce.admin.produits.edit', $produit->id) }}"
                       class="text-sm text-gray-700 hover:text-blue-600 truncate max-w-[160px]">{{ $produit->nom }}</a>
                    <span class="text-xs font-bold {{ $produit->stock === 0 ? 'text-red-600 bg-red-50' : 'text-orange-600 bg-orange-50' }} px-2 py-0.5 rounded-full">
                        {{ $produit->stock }} restant(s)
                    </span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('ecommerce.admin.produits.index', ['statut' => 'rupture']) }}"
               class="text-xs text-orange-600 hover:underline mt-3 block">Voir tous les produits en rupture →</a>
        </div>
        @endif

        {{-- Avis en attente --}}
        @if($avisEnAttente > 0)
        <div class="bg-white rounded-2xl border border-yellow-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                ⭐ <span>Avis en attente</span>
            </h3>
            <p class="text-2xl font-extrabold text-yellow-600">{{ $avisEnAttente }}</p>
            <p class="text-xs text-gray-400 mb-3">avis à modérer</p>
            <a href="{{ route('ecommerce.admin.avis.index') }}"
               class="text-xs text-yellow-600 hover:underline">Modérer les avis →</a>
        </div>
        @endif

        {{-- Raccourcis --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-3">Actions rapides</h3>
            <div class="space-y-2">
                <a href="{{ route('ecommerce.admin.produits.create') }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 py-1.5 transition-colors">
                    ➕ Ajouter un produit
                </a>
                <a href="{{ route('ecommerce.admin.coupons.create') }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 py-1.5 transition-colors">
                    🎫 Créer un coupon
                </a>
                <a href="{{ route('ecommerce.admin.commandes.export') }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 py-1.5 transition-colors">
                    📊 Exporter les commandes CSV
                </a>
                <a href="{{ route('ecommerce.admin.produits.export') }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 py-1.5 transition-colors">
                    📋 Exporter les produits CSV
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
