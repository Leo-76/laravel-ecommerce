@extends('ecommerce::layouts.admin')
@section('title', 'Produits')

@section('content')

{{-- Filtres & actions --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" action="{{ route('ecommerce.admin.produits.index') }}" class="flex gap-2 flex-1">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher..."
               class="border border-gray-200 rounded-xl px-4 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        <select name="statut" onchange="this.form.submit()"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
            <option value="">Tous les statuts</option>
            <option value="actif"   {{ request('statut') === 'actif'   ? 'selected' : '' }}>Actifs</option>
            <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactifs</option>
            <option value="rupture" {{ request('statut') === 'rupture' ? 'selected' : '' }}>En rupture</option>
            <option value="promo"   {{ request('statut') === 'promo'   ? 'selected' : '' }}>En promo</option>
        </select>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm hover:bg-gray-200">Filtrer</button>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('ecommerce.admin.produits.export') }}"
           class="border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-50">
            📊 Exporter CSV
        </a>
        <a href="{{ route('ecommerce.admin.produits.create') }}"
           class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
            ➕ Nouveau produit
        </a>
    </div>
</div>

{{-- Table produits --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="px-5 py-3.5">Produit</th>
                <th class="px-5 py-3.5">SKU</th>
                <th class="px-5 py-3.5">Catégories</th>
                <th class="px-5 py-3.5 text-right">Prix</th>
                <th class="px-5 py-3.5 text-right">Stock</th>
                <th class="px-5 py-3.5 text-center">Statut</th>
                <th class="px-5 py-3.5 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($produits as $produit)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $produit->image_principale_url }}" alt="{{ $produit->nom }}"
                             class="w-10 h-10 rounded-lg object-cover bg-gray-100 shrink-0">
                        <div>
                            <a href="{{ route('ecommerce.admin.produits.edit', $produit->id) }}"
                               class="font-medium text-gray-800 hover:text-blue-600 block max-w-[200px] truncate">
                                {{ $produit->nom }}
                            </a>
                            @if($produit->estEnPromo())
                            <span class="text-xs text-red-500 font-medium">🔥 En promo</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 font-mono text-xs text-gray-400">{{ $produit->sku ?? '—' }}</td>
                <td class="px-5 py-4 text-gray-500 text-xs max-w-[150px] truncate">
                    {{ $produit->categories->pluck('nom')->implode(', ') ?: '—' }}
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="font-semibold text-gray-900">@prixFormate($produit->prix_effectif)</div>
                    @if($produit->estEnPromo())
                    <div class="text-xs text-gray-400 line-through">@prixFormate($produit->prix)</div>
                    @endif
                </td>
                <td class="px-5 py-4 text-right">
                    <span class="font-semibold {{ $produit->stock === 0 ? 'text-red-600' : ($produit->aStockFaible() ? 'text-orange-500' : 'text-gray-800') }}">
                        {{ $produit->stock }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <button
                        onclick="toggleActif({{ $produit->id }}, this)"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition-colors
                            {{ $produit->actif ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-600 hover:bg-red-200' }}">
                        {{ $produit->actif ? '● Actif' : '○ Inactif' }}
                    </button>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('ecommerce.produits.show', $produit->slug) }}" target="_blank"
                           class="text-gray-400 hover:text-blue-600" title="Voir">👁</a>
                        <a href="{{ route('ecommerce.admin.produits.edit', $produit->id) }}"
                           class="text-gray-400 hover:text-blue-600" title="Modifier">✏️</a>
                        <form action="{{ route('ecommerce.admin.produits.destroy', $produit->id) }}" method="POST"
                              onsubmit="return confirm('Supprimer ce produit ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500" title="Supprimer">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                    <div class="text-4xl mb-2">📭</div>
                    <p>Aucun produit trouvé</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-gray-100">
        {{ $produits->links() }}
    </div>
</div>

@push('scripts')
<script>
async function toggleActif(id, btn) {
    const r = await fetch(`{{ url(config('ecommerce.prefix.admin', 'admin/boutique') . '/produits') }}/${id}/toggle-actif`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
    });
    const d = await r.json();
    btn.className = d.actif
        ? 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 hover:bg-green-200 transition-colors'
        : 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600 hover:bg-red-200 transition-colors';
    btn.textContent = d.actif ? '● Actif' : '○ Inactif';
}
</script>
@endpush
@endsection
