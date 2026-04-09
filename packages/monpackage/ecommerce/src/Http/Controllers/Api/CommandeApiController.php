<?php

namespace MonPackage\Ecommerce\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Commande;

class CommandeApiController extends Controller
{
    public function index()
    {
        $commandes = Commande::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return response()->json($commandes);
    }

    public function show(string $reference)
    {
        $commande = Commande::where('reference', $reference)
            ->where('user_id', auth()->id())
            ->with(['items.produit', 'historique'])
            ->firstOrFail();

        return response()->json($commande->append(['statut_libelle', 'total_formate']));
    }

    public function store(Request $request)
    {
        return app(\MonPackage\Ecommerce\Http\Controllers\CheckoutController::class)
            ->processerPaiement($request);
    }
}
