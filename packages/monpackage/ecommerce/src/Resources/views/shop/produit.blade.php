@extends('ecommerce::layouts.app')
@section('title', $produit->meta_titre ?? $produit->nom)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="produitPage({{ $produit->id }}, {{ json_encode($produit->variations->groupBy('valeurs.*.attribut.nom')) }})">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6 flex flex-wrap gap-1">
        <a href="{{ route('ecommerce.home') }}" class="hover:text-primary">Accueil</a>
        <span>/</span>
        <a href="{{ route('ecommerce.produits.index') }}" class="hover:text-primary">Catalogue</a>
        @if($produit->categories->isNotEmpty())
        <span>/</span>
        <a href="{{ route('ecommerce.categorie', $produit->categories->first()->slug) }}" class="hover:text-primary">
            {{ $produit->categories->first()->nom }}
        </a>
        @endif
        <span>/</span>
        <span class="text-gray-800 truncate max-w-[200px]">{{ $produit->nom }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

        {{-- Galerie --}}
        <div x-data="{ active: '{{ $produit->image_principale_url }}' }">
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 mb-3">
                <img :src="active" alt="{{ $produit->nom }}" class="w-full h-full object-cover">
            </div>
            @if($produit->images->isNotEmpty())
            <div class="flex gap-2 overflow-x-auto pb-2">
                <button @click="active = '{{ $produit->image_principale_url }}'"
                        class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-primary">
                    <img src="{{ $produit->image_principale_url }}" alt="" class="w-full h-full object-cover">
                </button>
                @foreach($produit->images as $img)
                <button @click="active = '{{ $img->url }}'"
                        class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition-colors">
                    <img src="{{ $img->url }}" alt="{{ $img->alt }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Infos produit --}}
        <div class="flex flex-col gap-5">

            {{-- Badges --}}
            <div class="flex gap-2 flex-wrap">
                @if($produit->estEnPromo())
                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">
                    🔥 -{{ $produit->pourcentage_promo }}% PROMO
                </span>
                @endif
                @if($produit->en_vedette)
                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">✨ Coup de cœur</span>
                @endif
                @if($produit->numerique)
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">💾 Produit numérique</span>
                @endif
            </div>

            {{-- Catégories --}}
            @if($produit->categories->isNotEmpty())
            <div class="flex gap-1 flex-wrap">
                @foreach($produit->categories as $cat)
                <a href="{{ route('ecommerce.categorie', $cat->slug) }}"
                   class="text-xs text-primary hover:underline border border-primary/30 px-2 py-0.5 rounded-full">
                    {{ $cat->nom }}
                </a>
                @endforeach
            </div>
            @endif

            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $produit->nom }}</h1>

            {{-- Note --}}
            @if($produit->avis->isNotEmpty())
            <div class="flex items-center gap-2">
                <div class="flex text-yellow-400">
                    @for($i = 1; $i <= 5; $i++)
                        <span>{{ $i <= round($produit->note_moyenne) ? '★' : '☆' }}</span>
                    @endfor
                </div>
                <span class="text-sm text-gray-500">{{ $produit->note_moyenne }}/5 ({{ $produit->avis->count() }} avis)</span>
                <a href="#avis" class="text-sm text-primary hover:underline">Voir les avis</a>
            </div>
            @endif

            {{-- Prix --}}
            <div class="flex items-end gap-3">
                <span class="text-3xl font-extrabold text-gray-900">@prixFormate($produit->prix_effectif)</span>
                @if($produit->estEnPromo())
                <span class="text-lg text-gray-400 line-through">@prixFormate($produit->prix)</span>
                @endif
            </div>

            {{-- Description courte --}}
            @if($produit->description_courte)
            <p class="text-gray-600 leading-relaxed">{{ $produit->description_courte }}</p>
            @endif

            {{-- Variations --}}
            @if($produit->variations->isNotEmpty())
            <div class="space-y-3">
                @foreach($produit->variations->first()?->valeurs->groupBy('attribut.nom') ?? [] as $attr => $valeurs)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $attr }}</label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach($valeurs->unique('valeur') as $val)
                        <button class="border-2 border-gray-200 rounded-lg px-3 py-1.5 text-sm hover:border-primary transition-colors"
                                :class="selected_{{ Str::slug($attr) }} === '{{ $val->valeur }}' ? 'border-primary bg-primary/5 text-primary font-semibold' : ''"
                                @click="selected_{{ Str::slug($attr) }} = '{{ $val->valeur }}'">
                            @if($val->couleur_hex)
                            <span class="inline-block w-4 h-4 rounded-full mr-1" style="background: {{ $val->couleur_hex }}"></span>
                            @endif
                            {{ $val->valeur }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Quantité + Ajouter --}}
            @if($produit->estEnStock())
            <div class="flex items-center gap-4">
                {{-- Quantité --}}
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="quantite = Math.max(1, quantite - 1)"
                            class="px-4 py-3 text-gray-600 hover:bg-gray-50 font-bold">−</button>
                    <span x-text="quantite" class="px-4 py-3 font-semibold min-w-[3rem] text-center"></span>
                    <button @click="quantite = Math.min({{ $produit->stock }}, quantite + 1)"
                            class="px-4 py-3 text-gray-600 hover:bg-gray-50 font-bold">+</button>
                </div>

                {{-- Ajouter au panier --}}
                <button @click="ajouterAuPanier()"
                        :disabled="chargement"
                        class="flex-1 flex items-center justify-center gap-2 bg-primary text-white font-semibold py-3 px-6 rounded-xl hover:bg-primary-dark transition-colors disabled:opacity-50 shadow-md shadow-primary/30">
                    <span x-show="!chargement">🛒 Ajouter au panier</span>
                    <span x-show="chargement">Ajout en cours...</span>
                </button>
            </div>

            <p class="text-sm text-green-600 font-medium">
                ✓ En stock
                @if($produit->aStockFaible())
                — <span class="text-orange-500">Plus que {{ $produit->stock }} disponible(s) !</span>
                @endif
            </p>
            @else
            <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-sm text-red-600 font-medium">
                😔 Ce produit est actuellement en rupture de stock.
            </div>
            @endif

            {{-- Wishlist --}}
            @auth
            <form action="{{ route('ecommerce.wishlist.toggle', $produit->id) }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-red-500 flex items-center gap-1 transition-colors">
                    ♡ Ajouter à ma liste de souhaits
                </button>
            </form>
            @endauth

            {{-- Infos livraison --}}
            <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm text-gray-600">
                <div class="flex items-center gap-2">🚚 <span>Livraison gratuite dès {{ number_format(config('ecommerce.livraison.gratuite_a_partir_de', 5000) / 100, 0) }}€</span></div>
                <div class="flex items-center gap-2">↩️ <span>Retours acceptés sous {{ config('ecommerce.retours.delai_jours', 14) }} jours</span></div>
                @if($produit->poids)
                <div class="flex items-center gap-2">⚖️ <span>Poids : {{ $produit->poids }} {{ $produit->unite_poids }}</span></div>
                @endif
                @if($produit->sku)
                <div class="flex items-center gap-2">🏷️ <span>Réf : {{ $produit->sku }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Description complète --}}
    @if($produit->description)
    <div class="mt-16">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Description complète</h2>
        <div class="prose max-w-none text-gray-600">
            {!! nl2br(e($produit->description)) !!}
        </div>
    </div>
    @endif

    {{-- Avis --}}
    <div id="avis" class="mt-16">
        <h2 class="text-xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">
            Avis clients ({{ $produit->avis->count() }})
        </h2>

        @if($produit->avis->isEmpty())
        <p class="text-gray-500 text-sm">Aucun avis pour l'instant. Soyez le premier !</p>
        @else
        <div class="space-y-4 mb-8">
            @foreach($produit->avis as $avis)
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-800">{{ $avis->auteur_nom }}</span>
                        @if($avis->achat_verifie)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">✓ Achat vérifié</span>
                        @endif
                    </div>
                    <div class="flex text-yellow-400 text-sm">
                        @for($i = 1; $i <= 5; $i++){{ $i <= $avis->note ? '★' : '☆' }}@endfor
                    </div>
                </div>
                @if($avis->titre)
                <p class="font-medium text-gray-700 mb-1">{{ $avis->titre }}</p>
                @endif
                @if($avis->contenu)
                <p class="text-sm text-gray-600">{{ $avis->contenu }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">{{ $avis->created_at->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Formulaire avis --}}
        @auth
        <div class="bg-blue-50 rounded-2xl border border-blue-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Laisser un avis</h3>
            <form action="{{ route('ecommerce.avis.store', $produit->slug) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="note" value="{{ $i }}" class="sr-only" required>
                            <span class="text-2xl text-gray-300 hover:text-yellow-400">★</span>
                        </label>
                        @endfor
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre (facultatif)</label>
                    <input type="text" name="titre" maxlength="100"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Votre avis</label>
                    <textarea name="contenu" rows="3" maxlength="1000"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"></textarea>
                </div>
                <button type="submit"
                        class="bg-primary text-white font-medium px-6 py-2 rounded-xl hover:bg-primary-dark transition-colors text-sm">
                    Publier mon avis
                </button>
            </form>
        </div>
        @else
        <p class="text-sm text-gray-500 bg-gray-50 rounded-xl p-4">
            <a href="{{ route('login') }}" class="text-primary hover:underline">Connectez-vous</a> pour laisser un avis.
        </p>
        @endauth
    </div>

    {{-- Produits similaires --}}
    @if($similaires->isNotEmpty())
    <div class="mt-16">
        <h2 class="text-xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-100">Vous aimerez aussi</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach($similaires as $produit)
                @include('ecommerce::shop.partials.carte-produit', compact('produit'))
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function produitPage(produitId) {
    return {
        quantite: 1,
        chargement: false,
        message: null,

        async ajouterAuPanier() {
            this.chargement = true;
            try {
                const r = await fetch('{{ route("ecommerce.panier.ajouter") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ produit_id: produitId, quantite: this.quantite })
                });
                const data = await r.json();
                if (data.success) {
                    window.dispatchEvent(new Event('panier-mis-a-jour'));
                }
                this.message = data.message;
            } catch(e) {
                this.message = 'Une erreur est survenue.';
            } finally {
                this.chargement = false;
                setTimeout(() => this.message = null, 3000);
            }
        }
    }
}
</script>
@endpush
@endsection
