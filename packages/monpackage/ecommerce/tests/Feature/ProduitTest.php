<?php

namespace MonPackage\Ecommerce\Tests\Feature;

use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Tests\TestCase;

class ProduitTest extends TestCase
{
    /** @test */
    public function il_cree_un_slug_automatiquement(): void
    {
        $produit = Produit::create([
            'nom'   => 'Mon Super Produit',
            'prix'  => 1990,
            'stock' => 10,
            'actif' => true,
        ]);

        $this->assertEquals('mon-super-produit', $produit->slug);
    }

    /** @test */
    public function il_evite_les_slugs_en_doublon(): void
    {
        Produit::create(['nom' => 'Produit Test', 'prix' => 1000, 'stock' => 5, 'actif' => true]);
        $produit2 = Produit::create(['nom' => 'Produit Test', 'prix' => 2000, 'stock' => 3, 'actif' => true]);

        $this->assertEquals('produit-test-1', $produit2->slug);
    }

    /** @test */
    public function il_retourne_le_prix_en_promo_si_actif(): void
    {
        $produit = Produit::create([
            'nom'       => 'Produit Promo',
            'prix'      => 5000,
            'prix_promo'=> 3990,
            'stock'     => 10,
            'actif'     => true,
        ]);

        $this->assertEquals(3990, $produit->prix_effectif);
        $this->assertTrue($produit->estEnPromo());
    }

    /** @test */
    public function il_retourne_le_prix_normal_hors_promo(): void
    {
        $produit = Produit::create([
            'nom'        => 'Produit Normal',
            'prix'       => 5000,
            'prix_promo' => 3990,
            'promo_fin'  => now()->subDay(),
            'stock'      => 10,
            'actif'      => true,
        ]);

        $this->assertEquals(5000, $produit->prix_effectif);
        $this->assertFalse($produit->estEnPromo());
    }

    /** @test */
    public function il_detecte_le_stock_faible(): void
    {
        $produit = Produit::create([
            'nom'   => 'Produit Faible Stock',
            'prix'  => 1000,
            'stock' => 3,
            'actif' => true,
        ]);

        $this->assertTrue($produit->aStockFaible());
        $this->assertTrue($produit->estEnStock());
    }

    /** @test */
    public function il_detecte_la_rupture_de_stock(): void
    {
        $produit = Produit::create([
            'nom'   => 'Produit Rupture',
            'prix'  => 1000,
            'stock' => 0,
            'actif' => true,
        ]);

        $this->assertFalse($produit->estEnStock());
    }

    /** @test */
    public function le_scope_actif_filtre_correctement(): void
    {
        Produit::create(['nom' => 'Actif',   'prix' => 1000, 'stock' => 5, 'actif' => true]);
        Produit::create(['nom' => 'Inactif', 'prix' => 1000, 'stock' => 5, 'actif' => false]);

        $actifs = Produit::actif()->get();
        $this->assertCount(1, $actifs);
        $this->assertEquals('Actif', $actifs->first()->nom);
    }

    /** @test */
    public function le_scope_recherche_fonctionne(): void
    {
        Produit::create(['nom' => 'iPhone 15 Pro', 'prix' => 100000, 'stock' => 5, 'actif' => true]);
        Produit::create(['nom' => 'Samsung Galaxy', 'prix' => 80000, 'stock' => 3, 'actif' => true]);

        $resultats = Produit::actif()->recherche('iPhone')->get();
        $this->assertCount(1, $resultats);
        $this->assertEquals('iPhone 15 Pro', $resultats->first()->nom);
    }
}
