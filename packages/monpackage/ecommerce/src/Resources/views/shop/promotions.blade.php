@extends('ecommerce::layouts.app')
@section('title', 'Promotions')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl p-8 text-white mb-8 text-center">
        <div class="text-5xl mb-2">🔥</div>
        <h1 class="text-3xl font-extrabold">Promotions en cours</h1>
        <p class="text-red-100 mt-1">Des offres à durée limitée — ne les manquez pas !</p>
    </div>
    @if($produits->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <p class="text-lg">Aucune promotion en ce moment. Revenez bientôt !</p>
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
