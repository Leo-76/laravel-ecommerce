<?php

namespace MonPackage\Ecommerce\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Categorie;

class CategorieApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Categorie::actif()
                ->with('enfants')
                ->whereNull('parent_id')
                ->orderBy('ordre')
                ->get()
        );
    }

    public function show(string $slug)
    {
        $categorie = Categorie::actif()
            ->with([
                'enfants',
                'produits' => fn($q) => $q->actif()->enStock()->with('images')->limit(10),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($categorie);
    }
}
