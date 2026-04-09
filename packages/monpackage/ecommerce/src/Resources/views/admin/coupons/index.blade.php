@extends('ecommerce::layouts.admin')
@section('title', 'Coupons de réduction')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">{{ $coupons->total() }} coupon(s)</p>
    <a href="{{ route('ecommerce.admin.coupons.create') }}"
       class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
        🎫 Nouveau coupon
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="px-5 py-3.5">Code</th>
                <th class="px-5 py-3.5">Type</th>
                <th class="px-5 py-3.5">Valeur</th>
                <th class="px-5 py-3.5 text-center">Utilisations</th>
                <th class="px-5 py-3.5">Validité</th>
                <th class="px-5 py-3.5 text-center">Statut</th>
                <th class="px-5 py-3.5 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($coupons as $coupon)
            <tr class="hover:bg-gray-50/50">
                <td class="px-5 py-4 font-mono font-bold text-gray-800 tracking-wide">{{ $coupon->code }}</td>
                <td class="px-5 py-4">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $coupon->type === 'pourcentage' ? 'bg-purple-100 text-purple-700' :
                           ($coupon->type === 'fixe' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                        {{ match($coupon->type) { 'pourcentage' => '% Pourcentage', 'fixe' => '€ Fixe', 'livraison' => '🚚 Livraison', default => $coupon->type } }}
                    </span>
                </td>
                <td class="px-5 py-4 font-semibold text-gray-800">
                    @if($coupon->type === 'pourcentage')
                        {{ $coupon->valeur }}%
                    @elseif($coupon->type === 'fixe')
                        @prixFormate($coupon->valeur)
                    @else
                        Livraison offerte
                    @endif
                </td>
                <td class="px-5 py-4 text-center text-gray-600">
                    {{ $coupon->utilisations_count }}
                    @if($coupon->utilisations_max)
                        / {{ $coupon->utilisations_max }}
                    @else
                        / ∞
                    @endif
                </td>
                <td class="px-5 py-4 text-xs text-gray-500">
                    @if($coupon->debut_at || $coupon->fin_at)
                        {{ $coupon->debut_at?->format('d/m/Y') ?? '…' }}
                        →
                        {{ $coupon->fin_at?->format('d/m/Y') ?? '∞' }}
                    @else
                        <span class="text-gray-400">Sans limite</span>
                    @endif
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $coupon->actif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $coupon->actif ? '● Actif' : '○ Inactif' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('ecommerce.admin.coupons.edit', $coupon->id) }}"
                           class="text-gray-400 hover:text-blue-600">✏️</a>
                        <form action="{{ route('ecommerce.admin.coupons.destroy', $coupon->id) }}"
                              method="POST" onsubmit="return confirm('Supprimer ce coupon ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                    <div class="text-4xl mb-2">🎫</div>
                    <p>Aucun coupon créé.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $coupons->links() }}
    </div>
</div>
@endsection
