@extends('ecommerce::layouts.admin')
@section('title', 'Commandes')

@section('content')

{{-- Filtres --}}
<form method="GET" action="{{ route('ecommerce.admin.commandes.index') }}" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Réf. ou email client..."
           class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
    <select name="statut" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
        <option value="">Tous les statuts</option>
        @foreach(['en_attente','confirmee','en_cours','expediee','livree','annulee','remboursee'] as $s)
        <option value="{{ $s }}" {{ request('statut') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
        @endforeach
    </select>
    <select name="paiement" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
        <option value="">Tout paiement</option>
        <option value="paye"       {{ request('paiement') === 'paye'       ? 'selected' : '' }}>Payé</option>
        <option value="en_attente" {{ request('paiement') === 'en_attente' ? 'selected' : '' }}>En attente</option>
        <option value="echec"      {{ request('paiement') === 'echec'      ? 'selected' : '' }}>Échec</option>
    </select>
    <input type="date" name="du" value="{{ request('du') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
    <input type="date" name="au" value="{{ request('au') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
    <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm hover:bg-gray-200">Filtrer</button>
    <a href="{{ route('ecommerce.admin.commandes.export') }}"
       class="ml-auto border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-50">
        📊 Exporter CSV
    </a>
</form>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="px-5 py-3.5">Référence</th>
                <th class="px-5 py-3.5">Client</th>
                <th class="px-5 py-3.5 text-right">Total</th>
                <th class="px-5 py-3.5 text-center">Statut</th>
                <th class="px-5 py-3.5 text-center">Paiement</th>
                <th class="px-5 py-3.5">Date</th>
                <th class="px-5 py-3.5 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($commandes as $commande)
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-4">
                    <a href="{{ route('ecommerce.admin.commandes.show', $commande->reference) }}"
                       class="font-mono font-bold text-blue-600 hover:underline text-xs tracking-wide">
                        {{ $commande->reference }}
                    </a>
                </td>
                <td class="px-5 py-4 text-gray-600">
                    {{ ($commande->adresse_livraison['prenom'] ?? '') . ' ' . ($commande->adresse_livraison['nom'] ?? '') }}
                    <div class="text-xs text-gray-400">{{ $commande->adresse_livraison['email'] ?? '' }}</div>
                </td>
                <td class="px-5 py-4 text-right font-bold text-gray-900">@prixFormate($commande->total)</td>
                <td class="px-5 py-4 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $commande->statut === 'livree' ? 'bg-green-100 text-green-700' :
                           ($commande->statut === 'annulee' ? 'bg-red-100 text-red-700' :
                           ($commande->statut === 'expediee' ? 'bg-blue-100 text-blue-700' :
                           'bg-yellow-100 text-yellow-700')) }}">
                        {{ $commande->statut_libelle }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $commande->statut_paiement === 'paye' ? 'bg-green-100 text-green-700' :
                           ($commande->statut_paiement === 'echec' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($commande->statut_paiement) }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-500 text-xs">{{ $commande->created_at->format('d/m/Y') }}</td>
                <td class="px-5 py-4 text-center">
                    <a href="{{ route('ecommerce.admin.commandes.show', $commande->reference) }}"
                       class="text-blue-600 hover:underline text-xs font-medium">Détail →</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                    <div class="text-4xl mb-2">📭</div>
                    <p>Aucune commande trouvée</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $commandes->links() }}
    </div>
</div>
@endsection
