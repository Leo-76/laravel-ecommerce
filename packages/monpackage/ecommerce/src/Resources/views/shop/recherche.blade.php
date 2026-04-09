@extends('ecommerce::layouts.app')
@section('title', 'Recherche : ' . $terme)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Résultats pour <span class="text-primary">« {{ $terme }} »</span>
        </h1>
        <p class="text-gray-500 text-sm mt-1">{{ $produits->total() }} résultat(s) trouvé(s)</p>
    </div>

    @if($produits->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">🔍</div>
        <p class="text-lg font-medium text-gray-600">Aucun produit trouvé pour « {{ $terme }} »</p>
        <p class="text-sm mt-2">Essayez avec d'autres mots-clés.</p>
        <a href="{{ route('ecommerce.produits.index') }}" class="mt-4 inline-block text-primary hover:underline">Voir tout le catalogue</a>
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
