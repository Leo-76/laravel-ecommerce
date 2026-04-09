<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Avis;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->calculerStats();
        $dernieresCommandes = Commande::with('client')
            ->latest()
            ->limit(10)
            ->get();

        $produitsFaibleStock = Produit::actif()
            ->where('stock', '<=', config('ecommerce.stock.seuil_alerte', 5))
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $avisEnAttente = Avis::where('approuve', false)->count();

        return view('ecommerce::admin.dashboard', compact(
            'stats', 'dernieresCommandes', 'produitsFaibleStock', 'avisEnAttente'
        ));
    }

    public function stats()
    {
        return response()->json($this->calculerStats());
    }

    private function calculerStats(): array
    {
        $maintenant     = now();
        $debutMois      = $maintenant->copy()->startOfMonth();
        $debutMoisDer   = $maintenant->copy()->subMonth()->startOfMonth();
        $finMoisDer     = $maintenant->copy()->subMonth()->endOfMonth();

        $caMois = Commande::where('statut_paiement', Commande::PAIEMENT_PAYE)
            ->whereBetween('created_at', [$debutMois, $maintenant])
            ->sum('total');

        $caMoisDer = Commande::where('statut_paiement', Commande::PAIEMENT_PAYE)
            ->whereBetween('created_at', [$debutMoisDer, $finMoisDer])
            ->sum('total');

        $evolutionCA = $caMoisDer > 0
            ? round((($caMois - $caMoisDer) / $caMoisDer) * 100, 1)
            : 100;

        return [
            'ca_mois'              => $caMois,
            'ca_mois_formate'      => number_format($caMois / 100, 2, ',', ' ') . ' €',
            'evolution_ca'         => $evolutionCA,
            'commandes_total'      => Commande::count(),
            'commandes_en_attente' => Commande::where('statut', Commande::STATUT_EN_ATTENTE)->count(),
            'commandes_mois'       => Commande::whereBetween('created_at', [$debutMois, $maintenant])->count(),
            'produits_total'       => Produit::actif()->count(),
            'produits_rupture'     => Produit::actif()->where('stock', 0)->count(),
            'panier_moyen'         => number_format(
                (Commande::where('statut_paiement', Commande::PAIEMENT_PAYE)->avg('total') ?? 0) / 100,
                2, ',', ' '
            ) . ' €',
            'ca_par_mois'          => $this->caParMois(),
        ];
    }

    private function caParMois(): array
    {
        return Commande::where('statut_paiement', Commande::PAIEMENT_PAYE)
            ->selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, SUM(total) as ca')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->limit(12)
            ->get()
            ->map(fn($r) => ['label' => $r->mois . '/' . $r->annee, 'ca' => $r->ca])
            ->toArray();
    }
}
