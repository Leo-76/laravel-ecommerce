@extends('ecommerce::layouts.app')
@section('title', 'Livraison')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Étapes --}}
    <div class="flex items-center justify-center gap-0 mb-10">
        @foreach(['Livraison' => 1, 'Paiement' => 2, 'Confirmation' => 3] as $etape => $num)
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $num === 1 ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                    {{ $num }}
                </div>
                <span class="text-xs mt-1 {{ $num === 1 ? 'text-primary font-semibold' : 'text-gray-400' }}">{{ $etape }}</span>
            </div>
            @if($num < 3)
            <div class="w-24 h-0.5 {{ $num < 1 ? 'bg-primary' : 'bg-gray-200' }} mx-1 mb-4"></div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Formulaire adresse --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">📦 Adresse de livraison</h2>

                <form action="{{ route('ecommerce.commande.livraison.save') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $adresse['prenom'] ?? auth()->user()?->name ?? '') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 @error('prenom') border-red-400 @enderror">
                            @error('prenom')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input type="text" name="nom" value="{{ old('nom', $adresse['nom'] ?? '') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                            @error('nom')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $adresse['email'] ?? auth()->user()?->email ?? '') }}"
                               required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone', $adresse['telephone'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $adresse['adresse'] ?? '') }}"
                               required placeholder="Numéro et nom de rue"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        @error('adresse')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complément</label>
                        <input type="text" name="complement" value="{{ old('complement', $adresse['complement'] ?? '') }}"
                               placeholder="Appartement, bâtiment, étage..."
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                            <input type="text" name="code_postal" value="{{ old('code_postal', $adresse['code_postal'] ?? '') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                            <input type="text" name="ville" value="{{ old('ville', $adresse['ville'] ?? '') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                        <select name="pays" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option value="FR" {{ old('pays', $adresse['pays'] ?? 'FR') === 'FR' ? 'selected' : '' }}>🇫🇷 France</option>
                            <option value="BE" {{ old('pays', $adresse['pays'] ?? '') === 'BE' ? 'selected' : '' }}>🇧🇪 Belgique</option>
                            <option value="CH" {{ old('pays', $adresse['pays'] ?? '') === 'CH' ? 'selected' : '' }}>🇨🇭 Suisse</option>
                            <option value="LU" {{ old('pays', $adresse['pays'] ?? '') === 'LU' ? 'selected' : '' }}>🇱🇺 Luxembourg</option>
                            <option value="DE" {{ old('pays', $adresse['pays'] ?? '') === 'DE' ? 'selected' : '' }}>🇩🇪 Allemagne</option>
                            <option value="ES" {{ old('pays', $adresse['pays'] ?? '') === 'ES' ? 'selected' : '' }}>🇪🇸 Espagne</option>
                        </select>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="meme_facturation" value="1" checked class="w-4 h-4 text-primary rounded">
                            <span class="text-sm text-gray-700">Adresse de facturation identique</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-primary-dark transition-colors shadow-md shadow-primary/30">
                            Continuer vers le paiement →
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Résumé commande --}}
        <div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                <h3 class="font-semibold text-gray-800 mb-4">Votre commande</h3>
                <div class="space-y-3 text-sm">
                    @foreach($resume['items'] as $item)
                    <div class="flex items-center gap-3">
                        <img src="{{ $item['image'] }}" alt="{{ $item['nom'] }}" class="w-10 h-10 rounded-lg object-cover bg-gray-50">
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium text-gray-700">{{ $item['nom'] }}</p>
                            <p class="text-gray-400 text-xs">× {{ $item['quantite'] }}</p>
                        </div>
                        <span class="font-semibold shrink-0">@prixFormate($item['prix'] * $item['quantite'])</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Sous-total</span><span>@prixFormate($resume['sous_total'])</span>
                    </div>
                    @if($resume['remise'] > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Réduction</span><span>−@prixFormate($resume['remise'])</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Livraison</span>
                        <span>{{ $resume['livraison'] === 0 ? 'Gratuite' : '' }}@if($resume['livraison'] > 0)@prixFormate($resume['livraison'])@endif</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-100">
                        <span>Total</span><span>@prixFormate($resume['total'])</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
