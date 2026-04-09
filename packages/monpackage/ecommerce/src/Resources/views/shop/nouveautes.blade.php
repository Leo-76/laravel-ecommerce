@extends('ecommerce::layouts.app')
@section('title', 'Nouveautés')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center gap-3 mb-8">
        <span class="text-3xl">🆕</span>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nouveautés</h1>
            <p class="text-gray-500 text-sm">Les derniers produits ajoutés à notre catalogue</p>
        </div>
    </div>
    @if($produits->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <p class="text-lg">Aucune nouveauté pour le moment.</p>
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
