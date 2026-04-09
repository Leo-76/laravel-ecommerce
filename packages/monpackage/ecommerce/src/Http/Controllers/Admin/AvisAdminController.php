<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Avis;

class AvisAdminController extends Controller
{
    public function index()
    {
        $avis = Avis::with('produit')->latest()->paginate(20);
        return view('ecommerce::admin.avis.index', compact('avis'));
    }

    public function approuver(int $id)
    {
        Avis::findOrFail($id)->update(['approuve' => true]);
        return back()->with('succes', 'Avis approuvé et publié.');
    }

    public function rejeter(int $id)
    {
        Avis::findOrFail($id)->update(['approuve' => false]);
        return back()->with('succes', 'Avis rejeté.');
    }

    public function destroy(int $id)
    {
        Avis::findOrFail($id)->delete();
        return back()->with('succes', 'Avis supprimé.');
    }
}
