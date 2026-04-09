@extends('ecommerce::layouts.app')
@section('title', 'Mon panier')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">🛒 Mon panier</h1>

    @panierVide
    <div class="text-center py-20">
        <div class="text-6xl mb-4">🛒</div>
        <p class="text-xl font-semibold text-gray-600 mb-2">Votre panier est vide</p>
        <p class="text-gray-500 mb-6">Découvrez nos produits et commencez vos achats !</p>
        <a href="{{ route('ecommerce.produits.index') }}"
           class="bg-primary text-white font-semibold px-8 py-3 rounded-full hover:bg-primary-dark transition-colors">
            Voir le catalogue
        </a>
    </div>
    @else
    <div class="flex flex-col lg:flex-row gap-8" x-data="panierPage()">

        {{-- Articles --}}
        <div class="flex-1 space-y-4">
            @foreach($resume['items'] as $cle => $item)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex gap-4"
                 id="item-{{ $cle }}">
                <img src="{{ $item['image'] }}" alt="{{ $item['nom'] }}"
                     class="w-24 h-24 object-cover rounded-xl bg-gray-50 shrink-0">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('ecommerce.produits.show', $item['slug']) }}"
                       class="font-semibold text-gray-800 hover:text-primary block truncate mb-1">
                        {{ $item['nom'] }}
                    </a>
                    @if(!empty($item['options']))
                    <p class="text-xs text-gray-400 mb-2">
                        {{ collect($item['options'])->map(fn($v,$k) => "$k: $v")->implode(', ') }}
                    </p>
                    @endif
                    <div class="flex items-center justify-between gap-4 flex-wrap mt-3">
                        {{-- Quantité --}}
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button onclick="modifierQte('{{ $cle }}', {{ $item['quantite'] - 1 }})"
                                    class="px-3 py-2 text-gray-600 hover:bg-gray-50 font-bold text-sm">−</button>
                            <span class="px-3 py-2 text-sm font-semibold min-w-[2.5rem] text-center">{{ $item['quantite'] }}</span>
                            <button onclick="modifierQte('{{ $cle }}', {{ $item['quantite'] + 1 }})"
                                    class="px-3 py-2 text-gray-600 hover:bg-gray-50 font-bold text-sm">+</button>
                        </div>
                        <div class="font-bold text-gray-900">
                            @prixFormate($item['prix'] * $item['quantite'])
                        </div>
                        <button onclick="supprimer('{{ $cle }}')"
                                class="text-red-400 hover:text-red-600 text-sm transition-colors">
                            🗑 Supprimer
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Coupon --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-3">🎫 Code de réduction</h3>
                @if($resume['coupon'])
                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                    <span class="text-green-700 font-semibold">{{ $resume['coupon']['code'] }} appliqué !</span>
                    <form action="{{ route('ecommerce.panier.coupon.retirer') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 text-sm hover:underline">Retirer</button>
                    </form>
                </div>
                @else
                <form action="{{ route('ecommerce.panier.coupon.appliquer') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="code" placeholder="Code promo"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 uppercase">
                    <button type="submit"
                            class="bg-gray-800 text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-gray-700 transition-colors">
                        Appliquer
                    </button>
                </form>
                @endif

                @error('code')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Récapitulatif --}}
        <div class="lg:w-80 shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                <h2 class="font-bold text-gray-800 text-lg mb-5">Récapitulatif</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Sous-total ({{ $resume['nombre_articles'] }} article(s))</span>
                        <span class="font-medium">@prixFormate($resume['sous_total'])</span>
                    </div>

                    @if($resume['remise'] > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Réduction ({{ $resume['coupon']['code'] }})</span>
                        <span class="font-medium">−@prixFormate($resume['remise'])</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-gray-600">
                        <span>Livraison</span>
                        <span class="font-medium">
                            @if($resume['livraison'] === 0)
                            <span class="text-green-600">Gratuite</span>
                            @else
                            @prixFormate($resume['livraison'])
                            @endif
                        </span>
                    </div>

                    @if($resume['tva'] > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>TVA</span>
                        <span class="font-medium">@prixFormate($resume['tva'])</span>
                    </div>
                    @endif

                    @if($resume['livraison'] > 0)
                    @php $manque = config('ecommerce.livraison.gratuite_a_partir_de') - ($resume['sous_total'] - $resume['remise']); @endphp
                    @if($manque > 0)
                    <p class="text-xs text-blue-600 bg-blue-50 rounded-lg p-2">
                        Plus que @prixFormate($manque) pour la livraison gratuite !
                    </p>
                    @endif
                    @endif
                </div>

                <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
                    <span class="font-bold text-gray-800">Total</span>
                    <span class="font-extrabold text-xl text-gray-900">@prixFormate($resume['total'])</span>
                </div>

                <a href="{{ route('ecommerce.commande.livraison') }}"
                   class="mt-5 block text-center bg-primary text-white font-semibold py-3 rounded-xl hover:bg-primary-dark transition-colors shadow-md shadow-primary/30">
                    Commander →
                </a>
                <a href="{{ route('ecommerce.produits.index') }}"
                   class="mt-2 block text-center text-sm text-gray-500 hover:text-primary transition-colors">
                    ← Continuer mes achats
                </a>
            </div>
        </div>
    </div>
    @endpanierVide
</div>

@push('scripts')
<script>
function panierPage() { return {} }

async function modifierQte(cle, quantite) {
    const r = await fetch(`/{{ config('ecommerce.prefix.shop', 'boutique') }}/panier/${cle}`, {
        method: 'PATCH',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({ quantite })
    });
    const d = await r.json();
    if (d.success) location.reload();
}

async function supprimer(cle) {
    if (!confirm('Retirer cet article ?')) return;
    const r = await fetch(`/{{ config('ecommerce.prefix.shop', 'boutique') }}/panier/${cle}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
    });
    const d = await r.json();
    if (d.success) location.reload();
}
</script>
@endpush
@endsection
