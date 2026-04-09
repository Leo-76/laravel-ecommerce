<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Avis;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $mesCommandes = Commande::where('user_id', $user->id)->latest()->limit(5)->get();
        $totalDepense = Commande::where('user_id', $user->id)->where('statut_paiement', 'paye')->sum('total');
        $commandesEnCours = Commande::where('user_id', $user->id)->whereNotIn('statut', ['livree', 'annulee', 'remboursee'])->count();
        $produitsVedette = Produit::actif()->enStock()->enVedette()->limit(4)->get();

        $statsAdmin = null;
        if ($user->isAdmin()) {
            $statsAdmin = [
                'commandes_today'  => Commande::whereDate('created_at', today())->count(),
                'ca_today'         => Commande::whereDate('created_at', today())->where('statut_paiement', 'paye')->sum('total'),
                'produits_rupture' => Produit::actif()->where('stock', 0)->count(),
                'avis_en_attente'  => Avis::where('approuve', false)->count(),
            ];
        }

        return view('dashboard', compact('mesCommandes', 'totalDepense', 'commandesEnCours', 'produitsVedette', 'statsAdmin'));
    }
}
