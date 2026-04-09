<?php

namespace MonPackage\Ecommerce\Tests\Feature;

use MonPackage\Ecommerce\Models\Commande;
use MonPackage\Ecommerce\Tests\TestCase;

class CommandeTest extends TestCase
{
    /** @test */
    public function elle_genere_une_reference_unique(): void
    {
        $commande = Commande::create([
            'statut'              => Commande::STATUT_EN_ATTENTE,
            'sous_total'          => 5000,
            'remise'              => 0,
            'livraison'           => 490,
            'tva'                 => 0,
            'total'               => 5490,
            'adresse_livraison'   => ['nom' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com', 'adresse' => '1 rue test', 'code_postal' => '75001', 'ville' => 'Paris', 'pays' => 'FR'],
            'adresse_facturation' => ['nom' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com', 'adresse' => '1 rue test', 'code_postal' => '75001', 'ville' => 'Paris', 'pays' => 'FR'],
        ]);

        $this->assertNotNull($commande->reference);
        $this->assertStringStartsWith('ECO-' . date('Y') . '-', $commande->reference);
    }

    /** @test */
    public function elle_peut_changer_de_statut(): void
    {
        $commande = $this->creerCommande();

        $this->assertEquals(Commande::STATUT_EN_ATTENTE, $commande->statut);

        $commande->changerStatut(Commande::STATUT_CONFIRMEE, 'Paiement reçu');

        $this->assertEquals(Commande::STATUT_CONFIRMEE, $commande->fresh()->statut);
        $this->assertCount(1, $commande->historique);
    }

    /** @test */
    public function elle_peut_etre_marquee_payee(): void
    {
        $commande = $this->creerCommande();

        $commande->marquerPaye('txn_test_123');

        $commande->refresh();
        $this->assertEquals(Commande::PAIEMENT_PAYE, $commande->statut_paiement);
        $this->assertEquals('txn_test_123', $commande->transaction_id);
        $this->assertNotNull($commande->paye_at);
        $this->assertTrue($commande->estPayee());
    }

    /** @test */
    public function elle_peut_etre_annulee_avant_expedition(): void
    {
        $commande = $this->creerCommande();
        $this->assertTrue($commande->peutEtreAnnulee());

        $commande->changerStatut(Commande::STATUT_EXPEDIEE);
        $this->assertFalse($commande->fresh()->peutEtreAnnulee());
    }

    private function creerCommande(): Commande
    {
        return Commande::create([
            'statut'              => Commande::STATUT_EN_ATTENTE,
            'sous_total'          => 10000,
            'remise'              => 0,
            'livraison'           => 0,
            'tva'                 => 0,
            'total'               => 10000,
            'adresse_livraison'   => ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean@test.fr', 'adresse' => '10 rue de la Paix', 'code_postal' => '75001', 'ville' => 'Paris', 'pays' => 'FR'],
            'adresse_facturation' => ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean@test.fr', 'adresse' => '10 rue de la Paix', 'code_postal' => '75001', 'ville' => 'Paris', 'pays' => 'FR'],
        ]);
    }
}
