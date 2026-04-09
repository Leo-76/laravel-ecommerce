<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Models\Retour;

class CompteController extends Controller
{
    public function commandes()
    {
        $commandes = Commande::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('ecommerce::shop.compte.commandes', compact('commandes'));
    }

    public function detailCommande(string $reference)
    {
        $commande = Commande::where('reference', $reference)
            ->where('user_id', auth()->id())
            ->with(['items.produit', 'items.variation', 'historique'])
            ->firstOrFail();

        return view('ecommerce::shop.compte.commande-detail', compact('commande'));
    }

    public function demandeRetour(Request $request, string $reference)
    {
        $commande = Commande::where('reference', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! config('ecommerce.retours.activer')) {
            return back()->with('erreur', 'Les retours ne sont pas activés.');
        }

        $delai = config('ecommerce.retours.delai_jours', 14);
        if ($commande->livre_at && $commande->livre_at->diffInDays(now()) > $delai) {
            return back()->with('erreur', "Le délai de retour de {$delai} jours est dépassé.");
        }

        $request->validate([
            'motif'       => 'required|string|in:' . implode(',', array_keys(config('ecommerce.retours.motifs', []))),
            'description' => 'nullable|string|max:500',
        ]);

        Retour::create([
            'commande_id' => $commande->id,
            'user_id'     => auth()->id(),
            'statut'      => 'demande',
            'motif'       => $request->motif,
            'description' => $request->description,
        ]);

        return back()->with('succes', 'Votre demande de retour a été enregistrée. Nous vous contacterons rapidement.');
    }

    public function profil()
    {
        return view('ecommerce::shop.compte.profil', ['user' => auth()->user()]);
    }

    public function updateProfil(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($validated);
        return back()->with('succes', 'Profil mis à jour.');
    }

    public function adresses()
    {
        return view('ecommerce::shop.compte.adresses');
    }
}
