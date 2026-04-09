@extends('ecommerce::layouts.app')
@section('title', $categorie->meta_titre ?? $categorie->nom . ' — ' . config('ecommerce.boutique.nom'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6 flex flex-wrap gap-1 items-center">
        <a href="{{ route('ecommerce.home') }}" class="hover:text-primary">Accueil</a>
        <span>/</span>
        <a href="{{ route('ecommerce.produits.index') }}" class="hover:text-primary">Catalogue</a>
        @if($categorie->parent)
        <span>/</span>
        <a href="{{ route('ecommerce.categorie', $categorie->parent->slug) }}" class="hover:text-primary">
            {{ $categorie->parent->nom }}
        </a>
        @endif
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $categorie->nom }}</span>
    </nav>

    {{-- En-tête catégorie --}}
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $categorie->nom }}</h1>
        @if($categorie->description)
        <p class="text-gray-500 max-w-2xl">{{ $categorie->description }}</p>
        @endif

        {{-- Sous-catégories --}}
        @if($categorie->enfants->isNotEmpty())
        <div class="flex flex-wrap gap-2 mt-4">
            @foreach($categorie->enfants as $sous)
            <a href="{{ route('ecommerce.categorie', $sous->slug) }}"
               class="border border-gray-200 text-gray-600 px-4 py-1.5 rounded-full text-sm hover:border-primary hover:text-primary transition-colors">
                {{ $sous->nom }}
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Résultats --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-800">{{ $produits->total() }}</span> produit(s)
        </p>
    </div>

    @if($produits->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <div class="text-5xl mb-3">📦</div>
        <p class="text-lg font-medium text-gray-600">Aucun produit dans cette catégorie.</p>
        <a href="{{ route('ecommerce.produits.index') }}" class="mt-4 inline-block text-primary hover:underline text-sm">
            Voir tout le catalogue
        </a>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($produits as $produit)
            @include('ecommerce::shop.partials.carte-produit', compact('produit'))
        @endforeach
    </div>
    <div class="mt-8">{{ $produits->links() }}</div>
    @endif
</div>
@endsection
