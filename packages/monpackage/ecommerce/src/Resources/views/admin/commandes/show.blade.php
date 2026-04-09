@extends('ecommerce::layouts.admin')
@section('title', 'Commande ' . $commande->reference)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Colonne principale --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Entête commande --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 font-mono">{{ $commande->reference }}</h2>
                    <p class="text-sm text-gray-400">Passée le {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1.5 rounded-full text-sm font-semibold
                        {{ $commande->statut === 'livree' ? 'bg-green-100 text-green-700' :
                           ($commande->statut === 'annulee' ? 'bg-red-100 text-red-700' :
                           ($commande->statut === 'expediee' ? 'bg-blue-100 text-blue-700' :
                           'bg-yellow-100 text-yellow-700')) }}">
                        {{ $commande->statut_libelle }}
                    </span>
                    <span class="px-3 py-1.5 rounded-full text-sm font-semibold
                        {{ $commande->statut_paiement === 'paye' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        💳 {{ ucfirst($commande->statut_paiement) }}
                    </span>
                </div>
            </div>

            {{-- Articles --}}
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase border-b border-gray-100">
                        <th class="pb-3">Produit</th>
                        <th class="pb-3 text-center">Qté</th>
                        <th class="pb-3 text-right">P.U.</th>
                        <th class="pb-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($commande->items as $item)
                    <tr>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                @if($item->produit)
                                <img src="{{ $item->produit->image_principale_url }}" alt=""
                                     class="w-8 h-8 rounded-lg object-cover bg-gray-100 shrink-0">
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->nom_produit }}</p>
                                    @if($item->sku_produit)
                                    <p class="text-xs text-gray-400 font-mono">{{ $item->sku_produit }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-center text-gray-600">{{ $item->quantite }}</td>
                        <td class="py-3 text-right text-gray-600">@prixFormate($item->prix_unitaire)</td>
                        <td class="py-3 text-right font-semibold">@prixFormate($item->total)</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Sous-total</span><span>@prixFormate($commande->sous_total)</span>
                </div>
                @if($commande->remise > 0)
                <div class="flex justify-between text-green-600">
                    <span>Réduction ({{ $commande->coupon_code }})</span><span>−@prixFormate($commande->remise)</span>
                </div>
                @endif
                <div class="flex justify-between text-gray-500">
                    <span>Livraison</span><span>{{ $commande->livraison === 0 ? 'Gratuite' : '' }}@if($commande->livraison > 0)@prixFormate($commande->livraison)@endif</span>
                </div>
                @if($commande->tva > 0)
                <div class="flex justify-between text-gray-500">
                    <span>TVA</span><span>@prixFormate($commande->tva)</span>
                </div>
                @endif
                <div class="flex justify-between font-extrabold text-gray-900 text-base pt-2 border-t border-gray-100">
                    <span>Total</span><span>@prixFormate($commande->total)</span>
                </div>
            </div>
        </div>

        {{-- Historique --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Historique des statuts</h3>
            <div class="space-y-3">
                @forelse($commande->historique as $h)
                <div class="flex gap-3 text-sm">
                    <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 shrink-0"></div>
                    <div>
                        <span class="font-medium text-gray-700">{{ ucfirst($h->statut_apres) }}</span>
                        @if($h->statut_avant)
                        <span class="text-gray-400"> ← {{ $h->statut_avant }}</span>
                        @endif
                        @if($h->commentaire)
                        <p class="text-gray-500 text-xs">{{ $h->commentaire }}</p>
                        @endif
                        <p class="text-gray-400 text-xs">{{ $h->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm">Aucun historique.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Colonne actions --}}
    <div class="space-y-5">

        {{-- Changer statut --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">Changer le statut</h3>
            <form action="{{ route('ecommerce.admin.commandes.statut', $commande->reference) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <select name="statut" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
                    @foreach(['en_attente', 'confirmee', 'en_cours', 'expediee', 'livree', 'annulee', 'remboursee'] as $s)
                    <option value="{{ $s }}" {{ $commande->statut === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <textarea name="commentaire" placeholder="Commentaire (facultatif)" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
                    Mettre à jour
                </button>
            </form>
        </div>

        {{-- Expédition --}}
        @if(!$commande->expedie_at)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">Marquer comme expédiée</h3>
            <form action="{{ route('ecommerce.admin.commandes.expedier', $commande->reference) }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="transporteur" placeholder="Transporteur (ex: Colissimo)" required
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
                <input type="text" name="numero_suivi" placeholder="Numéro de suivi" required
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-xl text-sm font-semibold hover:bg-green-700 transition-colors">
                    📦 Marquer expédiée
                </button>
            </form>
        </div>
        @endif

        {{-- Client --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-3">Client</h3>
            @php $adr = $commande->adresse_livraison; @endphp
            <div class="text-sm text-gray-600 space-y-1">
                <p class="font-medium text-gray-800">{{ ($adr['prenom'] ?? '') . ' ' . ($adr['nom'] ?? '') }}</p>
                <p>{{ $adr['email'] ?? '' }}</p>
                @if(!empty($adr['telephone']))<p>{{ $adr['telephone'] }}</p>@endif
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600 space-y-1">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Livraison</p>
                <p>{{ $adr['adresse'] ?? '' }}</p>
                @if(!empty($adr['complement']))<p>{{ $adr['complement'] }}</p>@endif
                <p>{{ ($adr['code_postal'] ?? '') . ' ' . ($adr['ville'] ?? '') }}</p>
                <p>{{ $adr['pays'] ?? '' }}</p>
            </div>
        </div>

        {{-- Paiement --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-3">Paiement</h3>
            <div class="text-sm text-gray-600 space-y-1">
                <p>Méthode : <span class="font-medium capitalize">{{ $commande->methode_paiement ?? '—' }}</span></p>
                <p>Statut : <span class="font-medium {{ $commande->estPayee() ? 'text-green-600' : 'text-orange-500' }}">{{ ucfirst($commande->statut_paiement) }}</span></p>
                @if($commande->transaction_id)
                <p class="text-xs text-gray-400 font-mono break-all">{{ $commande->transaction_id }}</p>
                @endif
                @if($commande->paye_at)
                <p class="text-xs text-gray-400">Payé le {{ $commande->paye_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('ecommerce.admin.commandes.facture', $commande->reference) }}"
                   target="_blank"
                   class="flex-1 text-center border border-gray-200 text-gray-600 py-2 rounded-xl text-xs hover:bg-gray-50">
                    🖨 Facture
                </a>
                @if($commande->estPayee())
                <form action="{{ route('ecommerce.admin.commandes.rembourser', $commande->reference) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Confirmer le remboursement ?')"
                            class="w-full border border-red-200 text-red-600 py-2 rounded-xl text-xs hover:bg-red-50">
                        ↩ Rembourser
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
