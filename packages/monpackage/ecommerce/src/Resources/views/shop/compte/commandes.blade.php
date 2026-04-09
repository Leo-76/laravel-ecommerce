@extends('ecommerce::layouts.app')
@section('title', 'Mes commandes')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📦 Mes commandes</h1>

    @if($commandes->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <div class="text-5xl mb-3">📭</div>
        <p class="text-lg font-medium text-gray-500">Vous n'avez pas encore de commande.</p>
        <a href="{{ route('ecommerce.produits.index') }}" class="mt-4 inline-block text-primary hover:underline">Découvrir nos produits →</a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($commandes as $commande)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                <div>
                    <span class="font-mono font-bold text-gray-800 tracking-wide">{{ $commande->reference }}</span>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $commande->statut === 'livree' ? 'bg-green-100 text-green-700' :
                           ($commande->statut === 'annulee' ? 'bg-red-100 text-red-700' :
                           ($commande->statut === 'expediee' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                        {{ $commande->statut_libelle }}
                    </span>
                    <span class="font-bold text-gray-800">@prixFormate($commande->total)</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($commande->items->take(3) as $item)
                <span class="text-xs bg-gray-50 border border-gray-100 rounded-lg px-2 py-1 text-gray-600">
                    {{ $item->nom_produit }} × {{ $item->quantite }}
                </span>
                @endforeach
                @if($commande->items->count() > 3)
                <span class="text-xs text-gray-400">+{{ $commande->items->count() - 3 }} autre(s)</span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('ecommerce.compte.commande', $commande->reference) }}"
                   class="text-sm text-primary hover:underline font-medium">Voir le détail →</a>
                @if($commande->numero_suivi)
                <span class="text-xs text-gray-500">Suivi : {{ $commande->numero_suivi }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $commandes->links() }}</div>
    @endif
</div>
@endsection
