<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Avis;
use MonPackage\Ecommerce\Models\Produit;

class AvisController extends Controller
{
    public function store(Request $request, string $produitSlug)
    {
        $produit = Produit::where('slug', $produitSlug)->firstOrFail();

        $validated = $request->validate([
            'note'    => 'required|integer|min:1|max:5',
            'titre'   => 'nullable|string|max:100',
            'contenu' => 'nullable|string|max:1000',
        ]);

        // Vérifier si l'utilisateur a déjà laissé un avis
        $existant = Avis::where('produit_id', $produit->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($existant) {
            return back()->with('erreur', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        // Vérifier achat si requis
        $achatVerifie = false;
        if (config('ecommerce.avis.achat_requis')) {
            $achatVerifie = auth()->user()
                ->commandes()
                ->where('statut_paiement', 'paye')
                ->whereHas('items', fn($q) => $q->where('produit_id', $produit->id))
                ->exists();
        }

        $approuve = ! config('ecommerce.avis.moderation', true);

        Avis::create([
            'produit_id'    => $produit->id,
            'user_id'       => auth()->id(),
            'auteur_nom'    => auth()->user()->name,
            'auteur_email'  => auth()->user()->email,
            'note'          => $validated['note'],
            'titre'         => $validated['titre'] ?? null,
            'contenu'       => $validated['contenu'] ?? null,
            'approuve'      => $approuve,
            'achat_verifie' => $achatVerifie,
        ]);

        $msg = $approuve
            ? 'Votre avis a été publié. Merci !'
            : 'Votre avis a été soumis et sera publié après modération.';

        return back()->with('succes', $msg);
    }

    public function destroy(int $id)
    {
        $avis = Avis::findOrFail($id);

        if ($avis->user_id !== auth()->id()) {
            abort(403);
        }

        $avis->delete();
        return back()->with('succes', 'Avis supprimé.');
    }
}
