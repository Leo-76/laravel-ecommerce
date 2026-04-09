<?php

namespace MonPackage\Ecommerce\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Services\PanierService;
use MonPackage\Ecommerce\Exceptions\PanierException;

class PanierApiController extends Controller
{
    public function __construct(private readonly PanierService $panier) {}

    public function index()
    {
        return response()->json($this->panier->resume());
    }

    public function ajouter(Request $request)
    {
        $r = $request->validate([
            'produit_id'   => 'required|integer|exists:eco_produits,id',
            'quantite'     => 'integer|min:1|max:99',
            'variation_id' => 'nullable|integer|exists:eco_variations,id',
        ]);

        try {
            return response()->json(
                $this->panier->ajouter($r['produit_id'], $r['quantite'] ?? 1, $r['variation_id'] ?? null)
            );
        } catch (PanierException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function modifier(Request $request, string $cle)
    {
        $r = $request->validate(['quantite' => 'required|integer|min:0|max:99']);

        try {
            return response()->json($this->panier->modifierQuantite($cle, $r['quantite']));
        } catch (PanierException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function supprimer(string $cle)
    {
        return response()->json($this->panier->supprimer($cle));
    }

    public function coupon(Request $request)
    {
        $r = $request->validate(['code' => 'required|string|max:50']);

        try {
            return response()->json($this->panier->appliquerCoupon($r['code']));
        } catch (PanierException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
