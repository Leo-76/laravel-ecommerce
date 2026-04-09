@extends('ecommerce::layouts.app')
@section('title', 'Commande ' . $commande->reference)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('ecommerce.compte.commandes') }}" class="text-gray-400 hover:text-gray-600">
            ← Mes commandes
        </a>
    </div>

    {{-- Statut & référence --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm text-gray-500 mb-1">Commande</p>
                <h1 class="text-2xl font-extrabold font-mono text-gray-900 tracking-wide">{{ $commande->reference }}</h1>
                <p class="text-sm text-gray-400 mt-1">Passée le {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    {{ $commande->statut === 'livree' ? 'bg-green-100 text-green-700' :
                       ($commande->statut === 'annulee' ? 'bg-red-100 text-red-700' :
                       ($commande->statut === 'expediee' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                    {{ $commande->statut_libelle }}
                </span>
                @if($commande->numero_suivi)
                <div class="mt-2 text-xs text-gray-500">
                    📦 Suivi : <span class="font-mono font-semibold text-blue-600">{{ $commande->numero_suivi }}</span>
                    @if($commande->transporteur)
                    ({{ $commande->transporteur }})
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Timeline statut --}}
        @php
            $etapes = [
                ['en_attente', 'En attente', '🕐'],
                ['confirmee',  'Confirmée',  '✅'],
                ['en_cours',   'En préparation', '📦'],
                ['expediee',   'Expédiée',   '🚚'],
                ['livree',     'Livrée',     '🏠'],
            ];
            $statuts = ['en_attente', 'confirmee', 'en_cours', 'expediee', 'livree'];
            $statutActuel = array_search($commande->statut, $statuts);
        @endphp

        @if(!in_array($commande->statut, ['annulee', 'remboursee']))
        <div class="mt-6 flex items-center justify-between relative">
            <div class="absolute left-0 right-0 top-4 h-0.5 bg-gray-200 z-0"></div>
            @foreach($etapes as $i => $etape)
            <div class="flex flex-col items-center relative z-10">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                    {{ $statutActuel !== false && $i <= $statutActuel
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 text-gray-400' }}">
                    {{ $statutActuel !== false && $i < $statutActuel ? '✓' : $etape[2] }}
                </div>
                <span class="text-xs mt-1 text-center
                    {{ $statutActuel !== false && $i <= $statutActuel ? 'text-blue-600 font-semibold' : 'text-gray-400' }}">
                    {{ $etape[1] }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Articles --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-800 mb-4">Articles</h2>
                <div class="space-y-4">
                    @foreach($commande->items as $item)
                    <div class="flex items-center gap-4">
                        @if($item->produit)
                        <img src="{{ $item->produit->image_principale_url }}"
                             alt="{{ $item->nom_produit }}"
                             class="w-14 h-14 object-cover rounded-xl bg-gray-50 shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate">{{ $item->nom_produit }}</p>
                            @if($item->sku_produit)
                            <p class="text-xs text-gray-400 font-mono">Réf : {{ $item->sku_produit }}</p>
                            @endif
                            <p class="text-sm text-gray-500">× {{ $item->quantite }}</p>
                        </div>
                        <p class="font-bold text-gray-900 shrink-0">@prixFormate($item->total)</p>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 mt-5 pt-5 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Sous-total</span><span>@prixFormate($commande->sous_total)</span>
                    </div>
                    @if($commande->remise > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Réduction ({{ $commande->coupon_code }})</span>
                        <span>−@prixFormate($commande->remise)</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-500">
                        <span>Livraison</span>
                        <span>{{ $commande->livraison === 0 ? 'Gratuite' : '' }}@if($commande->livraison > 0)@prixFormate($commande->livraison)@endif</span>
                    </div>
                    <div class="flex justify-between font-extrabold text-gray-900 text-base pt-2 border-t border-gray-100">
                        <span>Total</span><span>@prixFormate($commande->total)</span>
                    </div>
                </div>
            </div>

            {{-- Retour --}}
            @if(config('ecommerce.retours.activer') && in_array($commande->statut, ['livree']) && !$commande->retours->isNotEmpty())
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5">
                <h3 class="font-semibold text-gray-800 mb-3">↩️ Demander un retour</h3>
                <form action="{{ route('ecommerce.compte.retour', $commande->reference) }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="motif" required
                            class="w-full border border-orange-200 rounded-xl px-3 py-2 text-sm focus:outline-none bg-white">
                        <option value="">Choisir un motif...</option>
                        @foreach(config('ecommerce.retours.motifs', []) as $cle => $libelle)
                        <option value="{{ $cle }}">{{ $libelle }}</option>
                        @endforeach
                    </select>
                    <textarea name="description" rows="2" maxlength="500"
                              placeholder="Décrivez le problème (facultatif)..."
                              class="w-full border border-orange-200 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none bg-white"></textarea>
                    <button type="submit"
                            class="bg-orange-500 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-colors">
                        Soumettre la demande
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-2">Retours acceptés dans les {{ config('ecommerce.retours.delai_jours', 14) }} jours suivant la livraison.</p>
            </div>
            @endif
        </div>

        {{-- Sidebar infos --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Livraison</h3>
                @php $adr = $commande->adresse_livraison; @endphp
                <div class="text-sm text-gray-600 space-y-0.5">
                    <p class="font-medium text-gray-800">{{ ($adr['prenom'] ?? '') . ' ' . ($adr['nom'] ?? '') }}</p>
                    <p>{{ $adr['adresse'] ?? '' }}</p>
                    @if(!empty($adr['complement']))<p>{{ $adr['complement'] }}</p>@endif
                    <p>{{ ($adr['code_postal'] ?? '') . ' ' . ($adr['ville'] ?? '') }}</p>
                    <p>{{ $adr['pays'] ?? '' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Paiement</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>Méthode : <span class="font-medium capitalize">{{ $commande->methode_paiement ?? '—' }}</span></p>
                    <p class="text-green-600 font-medium">✓ Payée</p>
                    @if($commande->paye_at)
                    <p class="text-xs text-gray-400">Le {{ $commande->paye_at->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>

            {{-- Historique --}}
            @if($commande->historique->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Historique</h3>
                <div class="space-y-2">
                    @foreach($commande->historique->take(5) as $h)
                    <div class="flex gap-2 text-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 shrink-0"></span>
                        <div>
                            <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $h->statut_apres)) }}</span>
                            <span class="text-gray-400 block">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
