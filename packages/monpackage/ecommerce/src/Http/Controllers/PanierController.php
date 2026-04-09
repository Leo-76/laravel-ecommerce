<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Services\PanierService;
use MonPackage\Ecommerce\Exceptions\PanierException;

class PanierController extends Controller
{
    public function __construct(private readonly PanierService $panier) {}

    public function index()
    {
        return view('ecommerce::cart.index', [
            'resume' => $this->panier->resume(),
        ]);
    }

    public function ajouter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'produit_id'   => 'required|integer|exists:eco_produits,id',
            'quantite'     => 'integer|min:1|max:99',
            'variation_id' => 'nullable|integer|exists:eco_variations,id',
            'options'      => 'nullable|array',
        ]);

        try {
            $resume = $this->panier->ajouter(
                $validated['produit_id'],
                $validated['quantite'] ?? 1,
                $validated['variation_id'] ?? null,
                $validated['options'] ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'panier'  => $resume,
            ]);
        } catch (PanierException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function modifier(Request $request, string $cle): JsonResponse
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:0|max:99',
        ]);

        try {
            $resume = $this->panier->modifierQuantite($cle, $validated['quantite']);
            return response()->json(['success' => true, 'panier' => $resume]);
        } catch (PanierException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function supprimer(string $cle): JsonResponse
    {
        $resume = $this->panier->supprimer($cle);
        return response()->json(['success' => true, 'panier' => $resume]);
    }

    public function vider(): JsonResponse
    {
        $this->panier->vider();
        return response()->json(['success' => true, 'message' => 'Panier vidé']);
    }

    public function appliquerCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        try {
            $resume = $this->panier->appliquerCoupon($request->code);
            return response()->json(['success' => true, 'message' => 'Coupon appliqué !', 'panier' => $resume]);
        } catch (PanierException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function retirerCoupon(): JsonResponse
    {
        $resume = $this->panier->retirerCoupon();
        return response()->json(['success' => true, 'panier' => $resume]);
    }

    public function mini(): JsonResponse
    {
        return response()->json($this->panier->resume());
    }
}
