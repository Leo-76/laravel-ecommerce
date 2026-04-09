@extends('ecommerce::layouts.app')

@section('title', config('ecommerce.boutique.nom') . ' — Bienvenue')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-20 flex flex-col items-center text-center gap-6">
        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
            Bienvenue sur<br>{{ config('ecommerce.boutique.nom') }}
        </h1>
        <p class="text-blue-100 text-lg max-w-xl">Découvrez notre sélection de produits de qualité, livrés rapidement chez vous.</p>
        <div class="flex gap-3 flex-wrap justify-center">
            <a href="{{ route('ecommerce.produits.index') }}"
               class="bg-white text-blue-700 font-semibold px-8 py-3 rounded-full hover:bg-blue-50 transition-colors shadow-lg">
                Explorer le catalogue
            </a>
            <a href="{{ route('ecommerce.produits.promotions') }}"
               class="border border-white/50 text-white font-semibold px-8 py-3 rounded-full hover:bg-white/10 transition-colors">
                🔥 Promotions
            </a>
        </div>
    </div>
</section>

{{-- Produits en vedette --}}
@if(isset($produits) && $produits->count())
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-800">✨ Produits à la une</h2>
        <a href="{{ route('ecommerce.produits.index') }}" class="text-primary hover:underline text-sm font-medium">Voir tout →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($produits as $produit)
            @include('ecommerce::shop.partials.carte-produit', compact('produit'))
        @endforeach
    </div>
</section>
@endif

{{-- Avantages --}}
<section class="bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach([
            ['🚚', 'Livraison gratuite', 'Dès ' . number_format(config('ecommerce.livraison.gratuite_a_partir_de', 5000) / 100, 0) . '€ d\'achat'],
            ['↩️', 'Retours faciles', config('ecommerce.retours.delai_jours', 14) . ' jours pour changer d\'avis'],
            ['🔒', 'Paiement sécurisé', 'Vos données sont protégées'],
            ['⭐', 'Qualité garantie', 'Produits sélectionnés avec soin'],
        ] as [$icon, $titre, $desc])
        <div class="flex flex-col items-center gap-2">
            <span class="text-3xl">{{ $icon }}</span>
            <div class="font-semibold text-gray-800">{{ $titre }}</div>
            <div class="text-sm text-gray-500">{{ $desc }}</div>
        </div>
        @endforeach
    </div>
</section>

@endsection
