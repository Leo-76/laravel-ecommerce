@extends('ecommerce::layouts.admin')
@section('title', isset($coupon) ? 'Modifier le coupon' : 'Nouveau coupon')

@section('content')
<div class="max-w-2xl">
    <form action="{{ isset($coupon) ? route('ecommerce.admin.coupons.update', $coupon->id) : route('ecommerce.admin.coupons.store') }}"
          method="POST" class="space-y-5">
        @csrf
        @if(isset($coupon)) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code promo *</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}"
                       placeholder="ex: PROMO20, SOLDES2024..."
                       required maxlength="50" style="text-transform:uppercase"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono font-bold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('code') border-red-400 @enderror">
                @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                        <option value="pourcentage" {{ old('type', $coupon->type ?? '') === 'pourcentage' ? 'selected' : '' }}>% Pourcentage</option>
                        <option value="fixe"        {{ old('type', $coupon->type ?? '') === 'fixe'        ? 'selected' : '' }}>€ Montant fixe</option>
                        <option value="livraison"   {{ old('type', $coupon->type ?? '') === 'livraison'   ? 'selected' : '' }}>🚚 Livraison offerte</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valeur *</label>
                    <input type="number" name="valeur" step="0.01" min="0"
                           value="{{ old('valeur', isset($coupon) ? ($coupon->type === 'pourcentage' ? $coupon->valeur : $coupon->valeur / 100) : '') }}"
                           required placeholder="Ex: 20 (pour 20% ou 20€)"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum de commande (€)</label>
                    <input type="number" name="minimum_commande" step="0.01" min="0"
                           value="{{ old('minimum_commande', isset($coupon) && $coupon->minimum_commande ? $coupon->minimum_commande / 100 : '') }}"
                           placeholder="Laisser vide = sans minimum"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nb max. d'utilisations</label>
                    <input type="number" name="utilisations_max" min="1"
                           value="{{ old('utilisations_max', $coupon->utilisations_max ?? '') }}"
                           placeholder="Vide = illimité"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="datetime-local" name="debut_at"
                           value="{{ old('debut_at', $coupon->debut_at?->format('Y-m-d\TH:i') ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="datetime-local" name="fin_at"
                           value="{{ old('fin_at', $coupon->fin_at?->format('Y-m-d\TH:i') ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="actif" value="1"
                           {{ old('actif', ($coupon->actif ?? true) ? '1' : '') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700">Coupon actif</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
                {{ isset($coupon) ? '💾 Sauvegarder' : '🎫 Créer le coupon' }}
            </button>
            <a href="{{ route('ecommerce.admin.coupons.index') }}"
               class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl text-sm hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
