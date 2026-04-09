@extends('ecommerce::layouts.app')
@section('title', 'Paiement')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Étapes --}}
    <div class="flex items-center justify-center gap-0 mb-10">
        @foreach(['Livraison' => 1, 'Paiement' => 2, 'Confirmation' => 3] as $etape => $num)
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $num <= 2 ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                    {{ $num <= 1 ? '✓' : $num }}
                </div>
                <span class="text-xs mt-1 {{ $num === 2 ? 'text-primary font-semibold' : ($num < 2 ? 'text-green-600' : 'text-gray-400') }}">{{ $etape }}</span>
            </div>
            @if($num < 3)
            <div class="w-24 h-0.5 {{ $num < 2 ? 'bg-primary' : 'bg-gray-200' }} mx-1 mb-4"></div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">💳 Paiement sécurisé</h2>

                <form action="{{ route('ecommerce.commande.paiement.processer') }}" method="POST" id="form-paiement">
                    @csrf

                    {{-- Stripe --}}
                    @if($passerelle === 'stripe')
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 text-sm text-blue-700">
                        🔒 Vos données de carte sont chiffrées et sécurisées par Stripe. Nous ne stockons jamais vos informations bancaires.
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de carte</label>
                            <div id="card-element" class="border border-gray-200 rounded-xl px-4 py-3 bg-white"></div>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method_id" id="payment-method-id">

                    @elseif($passerelle === 'paypal')
                    <div id="paypal-button-container" class="my-4"></div>
                    <input type="hidden" name="paypal_order_id" id="paypal-order-id">

                    @elseif($passerelle === 'virement')
                    <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-5 space-y-2 text-sm text-gray-700">
                        <p class="font-semibold text-gray-800">📋 Coordonnées bancaires</p>
                        <p>IBAN : <span class="font-mono">FR76 XXXX XXXX XXXX XXXX XXXX XXX</span></p>
                        <p>BIC : <span class="font-mono">XXXXXXXX</span></p>
                        <p>Référence : <span class="font-mono font-bold">À COMPLÉTER APRÈS VALIDATION</span></p>
                        <p class="text-yellow-700 bg-yellow-100 rounded-lg p-2 mt-3">
                            ⚠️ Votre commande sera validée dès réception du virement (3 à 5 jours ouvrés).
                        </p>
                    </div>

                    @elseif($passerelle === 'especes')
                    <div class="bg-green-50 border border-green-100 rounded-xl p-5 text-sm text-gray-700">
                        <p class="font-semibold text-gray-800 mb-2">💵 Paiement à la livraison</p>
                        <p>Vous payerez directement au livreur lors de la réception de votre commande.</p>
                        <p class="mt-2 text-gray-500">Montant à préparer : <span class="font-bold text-gray-900">@prixFormate($resume['total'])</span></p>
                    </div>
                    @endif

                    {{-- Notes --}}
                    <div class="mt-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes pour la commande (facultatif)</label>
                        <textarea name="notes_client" rows="2" maxlength="500"
                                  placeholder="Instructions spéciales, horaires de livraison..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
                    </div>

                    <div class="mt-5">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" required class="mt-0.5 text-primary">
                            <span class="text-sm text-gray-600">
                                J'accepte les <a href="#" class="text-primary hover:underline">conditions générales de vente</a>
                                et la <a href="#" class="text-primary hover:underline">politique de confidentialité</a>.
                            </span>
                        </label>
                    </div>

                    @if(session('erreur'))
                    <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-sm text-red-600 mt-4">
                        {{ session('erreur') }}
                    </div>
                    @endif

                    <button type="submit" id="btn-payer"
                            class="mt-6 w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-primary-dark transition-colors shadow-lg shadow-primary/30 text-lg disabled:opacity-50">
                        🔒 Confirmer et payer @prixFormate($resume['total'])
                    </button>
                </form>
            </div>
        </div>

        {{-- Résumé --}}
        <div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                <h3 class="font-semibold text-gray-800 mb-4">Votre commande</h3>
                <div class="space-y-3 text-sm">
                    @foreach($resume['items'] as $item)
                    <div class="flex items-center gap-3">
                        <img src="{{ $item['image'] }}" alt="{{ $item['nom'] }}" class="w-10 h-10 rounded-lg object-cover bg-gray-50 shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium text-gray-700">{{ $item['nom'] }}</p>
                            <p class="text-gray-400 text-xs">× {{ $item['quantite'] }}</p>
                        </div>
                        <span class="font-semibold shrink-0">@prixFormate($item['prix'] * $item['quantite'])</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Sous-total</span><span>@prixFormate($resume['sous_total'])</span></div>
                    @if($resume['remise'] > 0)
                    <div class="flex justify-between text-green-600"><span>Réduction</span><span>−@prixFormate($resume['remise'])</span></div>
                    @endif
                    <div class="flex justify-between text-gray-600"><span>Livraison</span><span>{{ $resume['livraison'] === 0 ? 'Gratuite' : '' }}@if($resume['livraison'] > 0)@prixFormate($resume['livraison'])@endif</span></div>
                    <div class="flex justify-between font-extrabold text-gray-900 text-base pt-2 border-t border-gray-100">
                        <span>Total</span><span>@prixFormate($resume['total'])</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
