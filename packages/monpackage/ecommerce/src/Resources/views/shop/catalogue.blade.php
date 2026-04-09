@extends('ecommerce::layouts.app')
@section('title', 'Catalogue — ' . config('ecommerce.boutique.nom'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('ecommerce.home') }}" class="hover:text-primary">Accueil</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">Catalogue</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Filtres sidebar --}}
        <aside class="lg:w-64 shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm sticky top-24">
                <h2 class="font-semibold text-gray-800 mb-4">Filtres</h2>
                <form method="GET" action="{{ route('ecommerce.produits.index') }}" id="filtres-form">

                    {{-- Recherche --}}
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Recherche</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Mot-clé..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>

                    {{-- Catégories --}}
                    @if($categories->isNotEmpty())
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Catégorie</label>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="categorie" value="" {{ !request('categorie') ? 'checked' : '' }}
                                       class="text-primary" onchange="this.form.submit()">
                                <span class="text-sm text-gray-700">Toutes</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="categorie" value="{{ $cat->slug }}"
                                       {{ request('categorie') === $cat->slug ? 'checked' : '' }}
                                       class="text-primary" onchange="this.form.submit()">
                                <span class="text-sm text-gray-700">{{ $cat->nom }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $cat->produits()->count() }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Prix --}}
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Prix (€)</label>
                        <div class="flex gap-2">
                            <input type="number" name="prix_min" value="{{ request('prix_min') }}"
                                   placeholder="Min" min="0"
                                   class="w-1/2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <input type="number" name="prix_max" value="{{ request('prix_max') }}"
                                   placeholder="Max" min="0"
                                   class="w-1/2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        </div>
                    </div>

                    {{-- Promos --}}
                    <div class="mb-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="promo" value="1" {{ request('promo') ? 'checked' : '' }}
                                   class="text-red-500 rounded" onchange="this.form.submit()">
                            <span class="text-sm text-gray-700">🔥 En promotion seulement</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-primary text-white text-sm font-medium py-2 rounded-xl hover:bg-primary-dark transition-colors">
                        Appliquer les filtres
                    </button>

                    @if(request()->anyFilled(['q', 'categorie', 'prix_min', 'prix_max', 'promo']))
                    <a href="{{ route('ecommerce.produits.index') }}"
                       class="block text-center text-sm text-gray-500 hover:text-gray-800 mt-2">
                        Réinitialiser
                    </a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- Grille produits --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-800">{{ $produits->total() }}</span> produit(s)
                </p>

                {{-- Tri --}}
                <form method="GET" action="{{ route('ecommerce.produits.index') }}" class="flex items-center gap-2">
                    @foreach(request()->except('tri') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <label class="text-sm text-gray-500">Trier par :</label>
                    <select name="tri" onchange="this.form.submit()"
                            class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="nouveautes" {{ $tri === 'nouveautes' ? 'selected' : '' }}>Nouveautés</option>
                        <option value="prix_asc"   {{ $tri === 'prix_asc'   ? 'selected' : '' }}>Prix croissant</option>
                        <option value="prix_desc"  {{ $tri === 'prix_desc'  ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="popularite" {{ $tri === 'popularite' ? 'selected' : '' }}>Popularité</option>
                        <option value="note"        {{ $tri === 'note'       ? 'selected' : '' }}>Meilleures notes</option>
                    </select>
                </form>
            </div>

            @if($produits->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <div class="text-6xl mb-4">🔍</div>
                <p class="text-xl font-semibold text-gray-600">Aucun produit trouvé</p>
                <p class="mt-2 text-sm">Essayez de modifier vos filtres.</p>
                <a href="{{ route('ecommerce.produits.index') }}" class="mt-4 inline-block text-primary hover:underline text-sm">Voir tous les produits</a>
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($produits as $produit)
                    @include('ecommerce::shop.partials.carte-produit', compact('produit'))
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $produits->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
