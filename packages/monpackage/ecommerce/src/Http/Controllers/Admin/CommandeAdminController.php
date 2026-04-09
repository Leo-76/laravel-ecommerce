<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Commande;

class CommandeAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Commande::with('client')->latest();

        if ($request->filled('statut'))    { $query->where('statut', $request->statut); }
        if ($request->filled('paiement'))  { $query->where('statut_paiement', $request->paiement); }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) => $sq->where('reference', 'LIKE', "%{$q}%")
                ->orWhereJsonContains('adresse_livraison->email', $q));
        }
        if ($request->filled('du'))  { $query->whereDate('created_at', '>=', $request->du); }
        if ($request->filled('au'))  { $query->whereDate('created_at', '<=', $request->au); }

        $commandes = $query->paginate(20)->withQueryString();

        return view('ecommerce::admin.commandes.index', compact('commandes'));
    }

    public function show(string $reference)
    {
        $commande = Commande::with(['items.produit', 'items.variation', 'client', 'historique'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('ecommerce::admin.commandes.show', compact('commande'));
    }

    public function changerStatut(Request $request, string $reference)
    {
        $request->validate([
            'statut'      => 'required|string|in:en_attente,confirmee,en_cours,expediee,livree,annulee,remboursee',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $commande = Commande::where('reference', $reference)->firstOrFail();
        $commande->changerStatut($request->statut, $request->commentaire);

        return back()->with('succes', 'Statut mis à jour → ' . $request->statut);
    }

    public function expedier(Request $request, string $reference)
    {
        $request->validate([
            'transporteur'  => 'required|string|max:100',
            'numero_suivi'  => 'required|string|max:100',
        ]);

        $commande = Commande::where('reference', $reference)->firstOrFail();
        $commande->expedier($request->transporteur, $request->numero_suivi);

        return back()->with('succes', 'Commande marquée comme expédiée.');
    }

    public function rembourser(Request $request, string $reference)
    {
        $commande = Commande::where('reference', $reference)->firstOrFail();
        $commande->changerStatut(Commande::STATUT_REMBOURSEE, $request->motif);
        $commande->update(['statut_paiement' => Commande::PAIEMENT_REMBOURSE]);

        return back()->with('succes', 'Remboursement enregistré.');
    }

    public function facture(string $reference)
    {
        $commande = Commande::with(['items.produit', 'client'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('ecommerce::admin.commandes.facture', compact('commande'));
    }

    public function export(Request $request)
    {
        $commandes = Commande::latest()->get();
        $csv = "Référence,Date,Client,Email,Total,Statut,Paiement\n";
        foreach ($commandes as $c) {
            $email = $c->adresse_livraison['email'] ?? '';
            $nom   = ($c->adresse_livraison['prenom'] ?? '') . ' ' . ($c->adresse_livraison['nom'] ?? '');
            $csv  .= "\"{$c->reference}\",\"{$c->created_at->format('d/m/Y')}\",\"{$nom}\",\"{$email}\",\"" . number_format($c->total / 100, 2) . "\",\"{$c->statut}\",\"{$c->statut_paiement}\"\n";
        }
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="commandes-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
