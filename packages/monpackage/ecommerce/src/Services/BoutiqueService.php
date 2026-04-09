<?php

namespace MonPackage\Ecommerce\Services;

use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Categorie;

class BoutiqueService
{
    public function __construct(private readonly mixed $config) {}

    public function produitsVedette(int $limit = 8)
    {
        return Produit::actif()->enStock()->enVedette()->with('images')->limit($limit)->get();
    }

    public function nouveautes(int $limit = 8)
    {
        return Produit::actif()->enStock()->latest()->with('images')->limit($limit)->get();
    }

    public function promotions(int $limit = 8)
    {
        return Produit::actif()->enStock()->enPromo()->with('images')->limit($limit)->get();
    }

    public function categories()
    {
        return Categorie::actif()->whereNull('parent_id')->with('enfants')->orderBy('ordre')->get();
    }

    public function nom(): string
    {
        return $this->config->get('ecommerce.boutique.nom', 'Ma Boutique');
    }

    public function devise(): string
    {
        return $this->config->get('ecommerce.boutique.symbole', '€');
    }
}
