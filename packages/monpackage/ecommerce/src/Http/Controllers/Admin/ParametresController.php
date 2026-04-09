<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParametresController extends Controller
{
    public function index()
    {
        $config = config('ecommerce');
        return view('ecommerce::admin.parametres', compact('config'));
    }

    public function update(Request $request)
    {
        // En production, vous persisteriez les paramètres en base (table settings)
        // Pour l'instant, on redirige avec un message
        return back()->with('succes', 'Paramètres sauvegardés. Pensez à modifier config/ecommerce.php pour une persistance permanente.');
    }
}
