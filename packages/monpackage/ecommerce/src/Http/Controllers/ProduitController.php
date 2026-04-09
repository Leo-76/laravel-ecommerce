<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Categorie;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::actif()->enStock()->with(['categories', 'images']);

        // Filtres
        if ($request->filled('q')) {
            $query->recherche($request->q);
        }
        if ($request->filled('categorie')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->categorie));
        }
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', (int)($request->prix_min * 100));
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', (int)($request->prix_max * 100));
        }
        if ($request->boolean('promo')) {
            $query->enPromo();
        }

        // Tri
        $tri = $request->get('tri', config('ecommerce.catalogue.tri_defaut', 'nouveautes'));
        match ($tri) {
            'prix_asc'    => $query->orderBy('prix'),
            'prix_desc'   => $query->orderByDesc('prix'),
            'popularite'  => $query->orderByDesc('ventes_total'),
            'note'        => $query->withAvg('avis', 'note')->orderByDesc('avis_avg_note'),
            default       => $query->latest(),
        };

        $produits   = $query->paginate(config('ecommerce.catalogue.produits_par_page', 12))->withQueryString();
        $categories = Categorie::actif()->whereNull('parent_id')->with('enfants')->orderBy('ordre')->get();

        return view('ecommerce::shop.catalogue', compact('produits', 'categories', 'tri'));
    }

    public function show(string $slug)
    {
        $produit = Produit::actif()
            ->with(['categories', 'images', 'variations.valeurs.attribut', 'avis.auteur'])
            ->where('slug', $slug)
            ->firstOrFail();

        $similaires = Produit::actif()->enStock()
            ->whereHas('categories', fn ($q) => $q->whereIn('id', $produit->categories->pluck('id')))
            ->where('id', '!=', $produit->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('ecommerce::shop.produit', compact('produit', 'similaires'));
    }

    public function categorie(string $slug)
    {
        $categorie = Categorie::actif()->where('slug', $slug)->with('enfants')->firstOrFail();
        $produits  = Produit::actif()->enStock()
            ->whereHas('categories', fn ($q) => $q->where('slug', $slug))
            ->with(['categories', 'images'])
            ->paginate(config('ecommerce.catalogue.produits_par_page', 12));

        return view('ecommerce::shop.categorie', compact('categorie', 'produits'));
    }

    public function recherche(Request $request)
    {
        $terme    = $request->validate(['q' => 'required|string|min:2|max:100'])['q'];
        $produits = Produit::actif()->recherche($terme)->with('images')->paginate(12)->withQueryString();

        return view('ecommerce::shop.recherche', compact('produits', 'terme'));
    }

    public function promotions()
    {
        $produits = Produit::actif()->enStock()->enPromo()->with('images')->paginate(12);
        return view('ecommerce::shop.promotions', compact('produits'));
    }

    public function nouveautes()
    {
        $produits = Produit::actif()->enStock()->latest()->limit(24)->with('images')->paginate(12);
        return view('ecommerce::shop.nouveautes', compact('produits'));
    }

    public function stock(string $slug)
    {
        $produit = Produit::actif()->where('slug', $slug)->firstOrFail();
        return response()->json([
            'en_stock' => $produit->estEnStock(),
            'stock'    => $produit->stock,
            'faible'   => $produit->aStockFaible(),
        ]);
    }
}
