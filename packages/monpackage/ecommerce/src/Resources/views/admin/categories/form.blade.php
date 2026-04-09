@extends('ecommerce::layouts.admin')
@section('title', isset($categorie) ? 'Modifier : ' . $categorie->nom : 'Nouvelle catégorie')

@section('content')
<div class="max-w-2xl">
    <form action="{{ isset($categorie) ? route('ecommerce.admin.categories.update', $categorie->id) : route('ecommerce.admin.categories.store') }}"
          method="POST" class="space-y-5">
        @csrf
        @if(isset($categorie)) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Informations</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="nom" value="{{ old('nom', $categorie->nom ?? '') }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('nom') border-red-400 @enderror">
                @error('nom')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none">{{ old('description', $categorie->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie parente</label>
                    <select name="parent_id"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                        <option value="">— Aucune (racine) —</option>
                        @foreach($parents as $parent)
                        <option value="{{ $parent->id }}"
                            {{ old('parent_id', $categorie->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="ordre" min="0"
                           value="{{ old('ordre', $categorie->ordre ?? 0) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="actif" value="1"
                           {{ old('actif', ($categorie->actif ?? true) ? '1' : '') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700">Catégorie active</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
                {{ isset($categorie) ? '💾 Sauvegarder' : '➕ Créer' }}
            </button>
            <a href="{{ route('ecommerce.admin.categories.index') }}"
               class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl text-sm hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
