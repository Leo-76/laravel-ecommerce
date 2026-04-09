@extends('ecommerce::layouts.admin')
@section('title', 'Catégories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">{{ $categories->total() }} catégorie(s)</p>
    <a href="{{ route('ecommerce.admin.categories.create') }}"
       class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
        ➕ Nouvelle catégorie
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="px-5 py-3.5">Nom</th>
                <th class="px-5 py-3.5">Parent</th>
                <th class="px-5 py-3.5 text-center">Produits</th>
                <th class="px-5 py-3.5 text-center">Ordre</th>
                <th class="px-5 py-3.5 text-center">Statut</th>
                <th class="px-5 py-3.5 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($categories as $categorie)
            <tr class="hover:bg-gray-50/50">
                <td class="px-5 py-4">
                    <div class="font-medium text-gray-800">{{ $categorie->nom }}</div>
                    <div class="text-xs text-gray-400 font-mono">{{ $categorie->slug }}</div>
                </td>
                <td class="px-5 py-4 text-gray-500 text-xs">
                    {{ $categorie->parent?->nom ?? '—' }}
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="font-semibold text-gray-700">{{ $categorie->produits_count }}</span>
                </td>
                <td class="px-5 py-4 text-center text-gray-500">{{ $categorie->ordre }}</td>
                <td class="px-5 py-4 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $categorie->actif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $categorie->actif ? '● Actif' : '○ Inactif' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('ecommerce.admin.categories.edit', $categorie->id) }}"
                           class="text-gray-400 hover:text-blue-600 text-sm">✏️</a>
                        <form action="{{ route('ecommerce.admin.categories.destroy', $categorie->id) }}"
                              method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                    <div class="text-4xl mb-2">📂</div>
                    <p>Aucune catégorie. Commencez par en créer une.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $categories->links() }}
    </div>
</div>
@endsection
