@php
    $estPromo = $produit->estEnPromo();
@endphp
<div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col"
     x-data="{ ajout: false }">

    {{-- Image --}}
    <a href="{{ route('ecommerce.produits.show', $produit->slug) }}" class="relative block overflow-hidden aspect-square bg-gray-50">
        <img src="{{ $produit->image_principale_url }}"
             alt="{{ $produit->nom }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

        @if($estPromo)
        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
            -{{ $produit->pourcentage_promo }}%
        </span>
        @endif

        @if($produit->en_vedette)
        <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full">
            ✨ Coup de cœur
        </span>
        @endif

        @if(! $produit->estEnStock())
        <div class="absolute inset-0 bg-gray-900/50 flex items-center justify-center">
            <span class="bg-white text-gray-700 text-sm font-semibold px-3 py-1 rounded-full">Rupture de stock</span>
        </div>
        @endif
    </a>

    {{-- Infos --}}
    <div class="p-4 flex flex-col flex-1 gap-2">
        {{-- Catégories --}}
        @if($produit->categories->isNotEmpty())
        <div class="text-xs text-gray-400 truncate">{{ $produit->categories->pluck('nom')->implode(', ') }}</div>
        @endif

        {{-- Nom --}}
        <a href="{{ route('ecommerce.produits.show', $produit->slug) }}"
           class="font-semibold text-gray-800 hover:text-primary transition-colors line-clamp-2 leading-snug">
            {{ $produit->nom }}
        </a>

        {{-- Note --}}
        @if($produit->avis->isNotEmpty())
        <div class="flex items-center gap-1">
            <div class="flex text-yellow-400 text-xs">
                @for($i = 1; $i <= 5; $i++)
                    {{ $i <= round($produit->note_moyenne) ? '★' : '☆' }}
                @endfor
            </div>
            <span class="text-xs text-gray-400">({{ $produit->avis->count() }})</span>
        </div>
        @endif

        {{-- Prix + CTA --}}
        <div class="mt-auto flex items-center justify-between gap-2 pt-2">
            <div class="flex flex-col">
                <span class="font-bold text-gray-900">
                    @prixFormate($produit->prix_effectif)
                </span>
                @if($estPromo)
                <span class="text-xs text-gray-400 line-through">
                    @prixFormate($produit->prix)
                </span>
                @endif
            </div>

            @if($produit->estEnStock())
            <button
                @click="
                    ajout = true;
                    fetch('{{ route('ecommerce.panier.ajouter') }}', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
                        body: JSON.stringify({produit_id: {{ $produit->id }}, quantite: 1})
                    }).then(r => r.json()).then(d => {
                        if (d.success) window.dispatchEvent(new Event('panier-mis-a-jour'));
                        setTimeout(() => ajout = false, 1500);
                    });
                "
                class="flex items-center gap-1 bg-primary text-white text-sm font-medium px-3 py-2 rounded-xl hover:bg-primary-dark transition-colors disabled:opacity-50"
                :disabled="ajout">
                <span x-show="!ajout">🛒</span>
                <span x-show="ajout">✓</span>
            </button>
            @endif
        </div>
    </div>
</div>
