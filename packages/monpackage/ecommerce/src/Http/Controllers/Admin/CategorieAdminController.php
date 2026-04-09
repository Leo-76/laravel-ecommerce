<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Categorie;

class CategorieAdminController extends Controller
{
    public function index()
    {
        $categories = Categorie::with('parent', 'enfants')->withCount('produits')->orderBy('ordre')->paginate(20);
        return view('ecommerce::admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Categorie::actif()->whereNull('parent_id')->orderBy('nom')->get();
        return view('ecommerce::admin.categories.form', compact('parents'));
    }

    public function store(Request $request)
    {
        $r = $request->validate([
            'nom'         => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:eco_categories,id',
            'actif'       => 'boolean',
            'ordre'       => 'integer|min:0',
        ]);
        $cat = Categorie::create($r);
        return redirect()->route('ecommerce.admin.categories.index')->with('succes', "Catégorie « {$cat->nom} » créée.");
    }

    public function edit(int $id)
    {
        $categorie = Categorie::findOrFail($id);
        $parents   = Categorie::actif()->whereNull('parent_id')->where('id', '!=', $id)->orderBy('nom')->get();
        return view('ecommerce::admin.categories.form', compact('categorie', 'parents'));
    }

    public function update(Request $request, int $id)
    {
        $cat = Categorie::findOrFail($id);
        $r   = $request->validate([
            'nom'         => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:eco_categories,id',
            'actif'       => 'boolean',
            'ordre'       => 'integer|min:0',
        ]);
        $cat->update($r);
        return redirect()->route('ecommerce.admin.categories.index')->with('succes', 'Catégorie mise à jour.');
    }

    public function destroy(int $id)
    {
        Categorie::findOrFail($id)->delete();
        return back()->with('succes', 'Catégorie supprimée.');
    }

    public function reordonner(Request $request)
    {
        foreach ($request->ordre as $position => $id) {
            Categorie::where('id', $id)->update(['ordre' => $position]);
        }
        return response()->json(['succes' => true]);
    }
}
