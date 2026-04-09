@extends('ecommerce::layouts.admin')
@section('title', 'Avis clients')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="px-5 py-3.5">Auteur</th>
                <th class="px-5 py-3.5">Produit</th>
                <th class="px-5 py-3.5 text-center">Note</th>
                <th class="px-5 py-3.5">Contenu</th>
                <th class="px-5 py-3.5 text-center">Statut</th>
                <th class="px-5 py-3.5 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($avis as $a)
            <tr class="hover:bg-gray-50/50 {{ !$a->approuve ? 'bg-yellow-50/30' : '' }}">
                <td class="px-5 py-4">
                    <div class="font-medium text-gray-800">{{ $a->auteur_nom }}</div>
                    <div class="text-xs text-gray-400">{{ $a->auteur_email }}</div>
                    @if($a->achat_verifie)
                    <span class="text-xs text-green-600 bg-green-50 px-1.5 py-0.5 rounded">✓ Achat vérifié</span>
                    @endif
                </td>
                <td class="px-5 py-4">
                    @if($a->produit)
                    <a href="{{ route('ecommerce.produits.show', $a->produit->slug) }}" target="_blank"
                       class="text-blue-600 hover:underline text-xs">
                        {{ $a->produit->nom }}
                    </a>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex justify-center text-yellow-400">
                        @for($i = 1; $i <= 5; $i++){{ $i <= $a->note ? '★' : '☆' }}@endfor
                    </div>
                </td>
                <td class="px-5 py-4 max-w-xs">
                    @if($a->titre)
                    <p class="font-medium text-gray-700 text-xs">{{ $a->titre }}</p>
                    @endif
                    @if($a->contenu)
                    <p class="text-gray-500 text-xs truncate max-w-[200px]">{{ $a->contenu }}</p>
                    @endif
                    <p class="text-gray-400 text-xs mt-1">{{ $a->created_at->format('d/m/Y H:i') }}</p>
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $a->approuve ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $a->approuve ? '✓ Publié' : '⏳ En attente' }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center justify-center gap-2">
                        @if(!$a->approuve)
                        <form action="{{ route('ecommerce.admin.avis.approuver', $a->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" title="Approuver"
                                    class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded-lg font-medium">
                                ✓ Approuver
                            </button>
                        </form>
                        @else
                        <form action="{{ route('ecommerce.admin.avis.rejeter', $a->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" title="Dépublier"
                                    class="text-xs bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-2 py-1 rounded-lg font-medium">
                                ↩ Dépublier
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('ecommerce.admin.avis.destroy', $a->id) }}" method="POST"
                              onsubmit="return confirm('Supprimer cet avis ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 text-sm">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                    <div class="text-4xl mb-2">⭐</div>
                    <p>Aucun avis client pour le moment.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $avis->links() }}
    </div>
</div>
@endsection
