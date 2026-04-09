<?php

namespace MonPackage\Ecommerce\Tests\Feature;

use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Coupon;
use MonPackage\Ecommerce\Services\PanierService;
use MonPackage\Ecommerce\Exceptions\PanierException;
use MonPackage\Ecommerce\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PanierServiceTest extends TestCase
{
    use RefreshDatabase;

    private PanierService $panier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->panier = $this->app->make(PanierService::class);
    }

    /** @test */
    public function le_panier_est_vide_au_depart(): void
    {
        $this->assertTrue($this->panier->estVide());
        $this->assertEquals(0, $this->panier->count());
        $this->assertEquals(0, $this->panier->sousTotal());
    }

    /** @test */
    public function on_peut_ajouter_un_produit(): void
    {
        $produit = Produit::factory()->enStock()->create(['prix' => 2990]);

        $resume = $this->panier->ajouter($produit->id, 2);

        $this->assertFalse($this->panier->estVide());
        $this->assertEquals(2, $resume['nombre_articles']);
        $this->assertEquals(5980, $resume['sous_total']);
    }

    /** @test */
    public function on_ne_peut_pas_ajouter_un_produit_en_rupture(): void
    {
        $this->expectException(PanierException::class);

        $produit = Produit::factory()->rupture()->create();
        $this->panier->ajouter($produit->id, 1);
    }

    /** @test */
    public function la_quantite_saccumule_pour_le_meme_produit(): void
    {
        $produit = Produit::factory()->enStock()->create(['prix' => 1000, 'stock' => 20]);

        $this->panier->ajouter($produit->id, 1);
        $this->panier->ajouter($produit->id, 3);

        $this->assertEquals(4, $this->panier->count());
        $this->assertEquals(4000, $this->panier->sousTotal());
    }

    /** @test */
    public function on_peut_modifier_la_quantite(): void
    {
        $produit = Produit::factory()->enStock()->create(['prix' => 1000, 'stock' => 20]);
        $this->panier->ajouter($produit->id, 5);

        $items = $this->panier->items();
        $cle   = array_key_first($items);

        $resume = $this->panier->modifierQuantite($cle, 2);

        $this->assertEquals(2, $resume['nombre_articles']);
        $this->assertEquals(2000, $resume['sous_total']);
    }

    /** @test */
    public function modifier_a_zero_supprime_larticle(): void
    {
        $produit = Produit::factory()->enStock()->create(['prix' => 1000, 'stock' => 10]);
        $this->panier->ajouter($produit->id, 1);

        $items = $this->panier->items();
        $cle   = array_key_first($items);
        $this->panier->modifierQuantite($cle, 0);

        $this->assertTrue($this->panier->estVide());
    }

    /** @test */
    public function la_livraison_est_gratuite_au_dela_du_seuil(): void
    {
        config(['ecommerce.livraison.gratuite_a_partir_de' => 5000]);
        config(['ecommerce.livraison.forfait_defaut' => 490]);

        $produit = Produit::factory()->enStock()->create(['prix' => 6000, 'stock' => 5]);
        $this->panier->ajouter($produit->id, 1);

        $this->assertEquals(0, $this->panier->fraisLivraison());
    }

    /** @test */
    public function la_livraison_est_facturee_sous_le_seuil(): void
    {
        config(['ecommerce.livraison.gratuite_a_partir_de' => 5000]);
        config(['ecommerce.livraison.forfait_defaut' => 490]);

        $produit = Produit::factory()->enStock()->create(['prix' => 2000, 'stock' => 5]);
        $this->panier->ajouter($produit->id, 1);

        $this->assertEquals(490, $this->panier->fraisLivraison());
    }

    /** @test */
    public function un_coupon_pourcentage_reduit_le_total(): void
    {
        $produit = Produit::factory()->enStock()->create(['prix' => 10000, 'stock' => 5]);
        $this->panier->ajouter($produit->id, 1);

        Coupon::create([
            'code'    => 'TEST20',
            'type'    => 'pourcentage',
            'valeur'  => 20,
            'actif'   => true,
        ]);

        $this->panier->appliquerCoupon('TEST20');

        $this->assertEquals(2000, $this->panier->remise());
        $this->assertEquals(10000 - 2000, $this->panier->sousTotal() - $this->panier->remise());
    }

    /** @test */
    public function un_coupon_invalide_leve_une_exception(): void
    {
        $this->expectException(PanierException::class);

        $produit = Produit::factory()->enStock()->create(['prix' => 1000, 'stock' => 5]);
        $this->panier->ajouter($produit->id, 1);
        $this->panier->appliquerCoupon('INEXISTANT');
    }

    /** @test */
    public function vider_le_panier_le_remet_a_zero(): void
    {
        $produit = Produit::factory()->enStock()->create(['prix' => 1000, 'stock' => 5]);
        $this->panier->ajouter($produit->id, 2);

        $this->assertFalse($this->panier->estVide());

        $this->panier->vider();

        $this->assertTrue($this->panier->estVide());
        $this->assertEquals(0, $this->panier->sousTotal());
    }

    /** @test */
    public function le_resume_contient_toutes_les_cles_attendues(): void
    {
        $resume = $this->panier->resume();

        $this->assertArrayHasKey('items', $resume);
        $this->assertArrayHasKey('nombre_articles', $resume);
        $this->assertArrayHasKey('sous_total', $resume);
        $this->assertArrayHasKey('remise', $resume);
        $this->assertArrayHasKey('livraison', $resume);
        $this->assertArrayHasKey('tva', $resume);
        $this->assertArrayHasKey('total', $resume);
        $this->assertArrayHasKey('coupon', $resume);
    }
}
