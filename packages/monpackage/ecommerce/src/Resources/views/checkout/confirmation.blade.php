@extends('ecommerce::layouts.app')
@section('title', 'Commande confirmée — ' . $commande->reference)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">

    {{-- Succès header --}}
    <div class="text-center mb-10">
        <div class="text-7xl mb-4 animate-bounce">🎉</div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Merci pour votre commande !</h1>
        <p class="text-gray-500">Un email de confirmation a été envoyé à <strong>{{ $commande->adresse_livraison['email'] ?? '' }}</strong></p>
    </div>

    {{-- Numéro de commande --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 text-center mb-8">
        <p class="text-sm text-blue-600 mb-1">Numéro de commande</p>
        <p class="text-2xl font-extrabold text-blue-700 tracking-wider">{{ $commande->reference }}</p>
        <p class="text-xs text-blue-500 mt-1">Conservez ce numéro pour suivre votre commande</p>
    </div>

    {{-- Détail commande --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="font-bold text-gray-800 mb-4">Récapitulatif</h2>
        <div class="space-y-3">
            @foreach($commande->items as $item)
            <div class="flex items-center gap-4">
                @if($item->produit)
                <img src="{{ $item->produit->image_principale_url }}" alt="{{ $item->nom_produit }}"
                     class="w-12 h-12 object-cover rounded-xl bg-gray-50 shrink-0">
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate">{{ $item->nom_produit }}</p>
                    <p class="text-sm text-gray-400">× {{ $item->quantite }}</p>
                </div>
                <p class="font-semibold text-gray-800">@prixFormate($item->total)</p>
            </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 mt-5 pt-5 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Sous-total</span><span>@prixFormate($commande->sous_total)</span>
            </div>
            @if($commande->remise > 0)
            <div class="flex justify-between text-green-600">
                <span>Réduction ({{ $commande->coupon_code }})</span><span>−@prixFormate($commande->remise)</span>
            </div>
            @endif
            <div class="flex justify-between text-gray-600">
                <span>Livraison</span>
                <span>{{ $commande->livraison === 0 ? 'Gratuite' : '' }}@if($commande->livraison > 0)@prixFormate($commande->livraison)@endif</span>
            </div>
            <div class="flex justify-between font-extrabold text-gray-900 text-lg pt-2 border-t border-gray-100">
                <span>Total payé</span><span>@prixFormate($commande->total)</span>
            </div>
        </div>
    </div>

    {{-- Adresse --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wide">Livraison</h3>
            @php $adr = $commande->adresse_livraison; @endphp
            <p class="text-sm text-gray-600">{{ $adr['prenom'] ?? '' }} {{ $adr['nom'] ?? '' }}</p>
            <p class="text-sm text-gray-600">{{ $adr['adresse'] ?? '' }}</p>
            @if(!empty($adr['complement']))<p class="text-sm text-gray-600">{{ $adr['complement'] }}</p>@endif
            <p class="text-sm text-gray-600">{{ $adr['code_postal'] ?? '' }} {{ $adr['ville'] ?? '' }}</p>
            <p class="text-sm text-gray-600">{{ $adr['pays'] ?? '' }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wide">Paiement</h3>
            <p class="text-sm text-gray-600">Méthode : <span class="font-medium capitalize">{{ $commande->methode_paiement }}</span></p>
            <p class="text-sm text-gray-600">Statut : <span class="font-medium text-green-600">✓ Payé</span></p>
            @if($commande->transaction_id)
            <p class="text-xs text-gray-400 mt-1">Réf. : {{ $commande->transaction_id }}</p>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        @auth
        <a href="{{ route('ecommerce.compte.commande', $commande->reference) }}"
           class="bg-primary text-white font-semibold px-6 py-3 rounded-xl hover:bg-primary-dark transition-colors text-center">
            Suivre ma commande
        </a>
        @endauth
        <a href="{{ route('ecommerce.produits.index') }}"
           class="border border-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:bg-gray-50 transition-colors text-center">
            Continuer mes achats
        </a>
    </div>
</div>
@endsection
