@extends('ecommerce::layouts.admin')
@section('title', isset($produit) ? 'Modifier : ' . $produit->nom : 'Nouveau produit')

@section('content')

<div class="max-w-4xl">
    <form action="{{ isset($produit) ? route('ecommerce.admin.produits.update', $produit->id) : route('ecommerce.admin.produits.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($produit)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Infos principales --}}
            <div class="lg:col-span-2 space-y-5">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Informations</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
                        <input type="text" name="nom" value="{{ old('nom', $produit->nom ?? '') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('nom') border-red-400 @enderror">
                        @error('nom')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                        <textarea name="description_courte" rows="2" maxlength="500"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none">{{ old('description_courte', $produit->description_courte ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description complète</label>
                        <textarea name="description" rows="6"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none">{{ old('description', $produit->description ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $produit->sku ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Poids (kg)</label>
                            <input type="number" name="poids" step="0.001" min="0" value="{{ old('poids', $produit->poids ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Prix & Stock</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix (€) *</label>
                            <input type="number" name="prix" step="0.01" min="0"
                                   value="{{ old('prix', isset($produit) ? $produit->prix / 100 : '') }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('prix') border-red-400 @enderror">
                            @error('prix')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix promo (€)</label>
                            <input type="number" name="prix_promo" step="0.01" min="0"
                                   value="{{ old('prix_promo', isset($produit) && $produit->prix_promo ? $produit->prix_promo / 100 : '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Début promo</label>
                            <input type="date" name="promo_debut" value="{{ old('promo_debut', $produit->promo_debut?->format('Y-m-d') ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fin promo</label>
                            <input type="date" name="promo_fin" value="{{ old('promo_fin', $produit->promo_fin?->format('Y-m-d') ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                            <input type="number" name="stock" min="0" value="{{ old('stock', $produit->stock ?? 0) }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">TVA (%)</label>
                            <select name="tva" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                                @foreach([0, 5, 10, 20] as $taux)
                                <option value="{{ $taux }}" {{ old('tva', $produit->tva ?? 20) == $taux ? 'selected' : '' }}>{{ $taux }}%</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">SEO</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta titre (70 car. max)</label>
                        <input type="text" name="meta_titre" maxlength="70" value="{{ old('meta_titre', $produit->meta_titre ?? '') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta description (160 car. max)</label>
                        <textarea name="meta_description" rows="2" maxlength="160"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none">{{ old('meta_description', $produit->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Sidebar options --}}
            <div class="space-y-5">

                {{-- Publication --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-4">Publication</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="actif" value="1" {{ old('actif', ($produit->actif ?? true) ? '1' : '') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">Produit actif (visible sur la boutique)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="en_vedette" value="1" {{ old('en_vedette', ($produit->en_vedette ?? false) ? '1' : '') ? 'checked' : '' }}
                                   class="w-4 h-4 text-yellow-500 rounded">
                            <span class="text-sm text-gray-700">✨ Produit en vedette</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="numerique" value="1" {{ old('numerique', ($produit->numerique ?? false) ? '1' : '') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">💾 Produit numérique</span>
                        </label>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
                            {{ isset($produit) ? '💾 Sauvegarder' : '➕ Créer le produit' }}
                        </button>
                        <a href="{{ route('ecommerce.admin.produits.index') }}"
                           class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">
                            Annuler
                        </a>
                    </div>
                </div>

                {{-- Catégories --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-4">Catégories</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                   {{ (isset($produit) && $produit->categories->contains($cat->id)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">{{ $cat->nom }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Image principale --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Image principale</h3>
                    @if(isset($produit) && $produit->image_principale)
                    <img src="{{ $produit->image_principale_url }}" alt="" class="w-full h-40 object-cover rounded-xl mb-3 bg-gray-50">
                    @endif
                    <input type="file" name="image_principale" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:text-xs file:font-medium hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max {{ config('ecommerce.images.taille_max_mo') }}Mo</p>
                </div>

                {{-- Images galerie --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Galerie</h3>
                    @if(isset($produit) && $produit->images->isNotEmpty())
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        @foreach($produit->images as $img)
                        <div class="relative group">
                            <img src="{{ $img->url }}" alt="" class="w-full h-16 object-cover rounded-lg">
                            <button type="button"
                                    onclick="supprimerImage({{ $produit->id }}, {{ $img->id }}, this.parentElement)"
                                    class="absolute top-0.5 right-0.5 bg-red-500 text-white w-5 h-5 rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <input type="file" name="images[]" accept="image/*" multiple
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-gray-50 file:text-gray-700 file:text-xs hover:file:bg-gray-100">
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
async function supprimerImage(produitId, imageId, el) {
    if (!confirm('Supprimer cette image ?')) return;
    const r = await fetch(`{{ url(config('ecommerce.prefix.admin', 'admin/boutique') . '/produits') }}/${produitId}/image/${imageId}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
    });
    if ((await r.json()).succes) el.remove();
}
</script>
@endpush
@endsection
