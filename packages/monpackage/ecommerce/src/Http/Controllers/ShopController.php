<?php

namespace MonPackage\Ecommerce\Http\Controllers;

use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Services\BoutiqueService;

class ShopController extends Controller
{
    public function __construct(private readonly BoutiqueService $boutique) {}

    public function index()
    {
        $produits = $this->boutique->produitsVedette(8);
        return view('ecommerce::shop.home', compact('produits'));
    }
}
