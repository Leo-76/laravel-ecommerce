@extends('ecommerce::layouts.app')
@section('title', 'Ma wishlist')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">❤️ Ma liste de souhaits</h1>

    @if($produits->isEmpty())
    <div class="text-center py-20">
        <div class="text-6xl mb-4">💝</div>
        <p class="text-xl font-semibold text-gray-600 mb-2">Votre wishlist est vide</p>
        <p class="text-gray-500 mb-6 text-sm">Ajoutez vos produits favoris en cliquant sur le cœur.</p>
        <a href="{{ route('ecommerce.produits.index') }}"
           class="bg-primary text-white font-semibold px-8 py-3 rounded-full hover:bg-primary-dark transition-colors">
            Explorer le catalogue
        </a>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($produits as $produit)
        <div class="relative">
            @include('ecommerce::shop.partials.carte-produit', compact('produit'))

            {{-- Bouton retirer de la wishlist --}}
            <form action="{{ route('ecommerce.wishlist.toggle', $produit->id) }}"
                  method="POST"
                  class="absolute top-2 right-2">
                @csrf
                <button type="submit"
                        class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors text-sm"
                        title="Retirer de la wishlist">
                    ❤️
                </button>
            </form>
        </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $produits->links() }}</div>
    @endif
</div>
@endsection
