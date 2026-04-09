@extends('ecommerce::layouts.app')
@section('title', 'Mes adresses')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📍 Mes adresses</h1>

    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 text-sm text-blue-700">
        <p class="font-semibold mb-1">Adresses sauvegardées depuis vos commandes</p>
        <p>Vos adresses de livraison sont automatiquement pré-remplies lors de votre prochain achat, depuis votre dernière commande.</p>
    </div>

    @php
        $derniereCommande = auth()->user()->commandes()->latest()->first();
    @endphp

    @if($derniereCommande)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Dernière livraison</h3>
            @php $adr = $derniereCommande->adresse_livraison; @endphp
            <address class="text-sm text-gray-600 not-italic space-y-0.5">
                <p class="font-semibold text-gray-800">{{ ($adr['prenom'] ?? '') . ' ' . ($adr['nom'] ?? '') }}</p>
                <p>{{ $adr['adresse'] ?? '' }}</p>
                @if(!empty($adr['complement']))<p>{{ $adr['complement'] }}</p>@endif
                <p>{{ ($adr['code_postal'] ?? '') . ' ' . ($adr['ville'] ?? '') }}</p>
                <p>{{ $adr['pays'] ?? '' }}</p>
                @if(!empty($adr['telephone']))<p class="text-gray-400 text-xs mt-1">{{ $adr['telephone'] }}</p>@endif
            </address>
            <p class="text-xs text-gray-400 mt-3">Commande {{ $derniereCommande->reference }} — {{ $derniereCommande->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Dernière facturation</h3>
            @php $fac = $derniereCommande->adresse_facturation; @endphp
            <address class="text-sm text-gray-600 not-italic space-y-0.5">
                <p class="font-semibold text-gray-800">{{ ($fac['prenom'] ?? '') . ' ' . ($fac['nom'] ?? '') }}</p>
                <p>{{ $fac['adresse'] ?? '' }}</p>
                @if(!empty($fac['complement']))<p>{{ $fac['complement'] }}</p>@endif
                <p>{{ ($fac['code_postal'] ?? '') . ' ' . ($fac['ville'] ?? '') }}</p>
                <p>{{ $fac['pays'] ?? '' }}</p>
            </address>
        </div>
    </div>
    @else
    <div class="text-center py-12 text-gray-400 mt-6">
        <div class="text-5xl mb-3">📍</div>
        <p class="font-medium text-gray-500">Aucune adresse enregistrée.</p>
        <p class="text-sm mt-1">Vos adresses apparaîtront après votre première commande.</p>
        <a href="{{ route('ecommerce.produits.index') }}"
           class="mt-4 inline-block text-primary hover:underline text-sm">Commencer mes achats →</a>
    </div>
    @endif
</div>
@endsection
