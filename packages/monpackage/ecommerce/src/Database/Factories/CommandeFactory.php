<?php

namespace MonPackage\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MonPackage\Ecommerce\Models\Commande;

class CommandeFactory extends Factory
{
    protected $model = Commande::class;

    public function definition(): array
    {
        $sousTotal = $this->faker->numberBetween(2000, 150000);
        $livraison = $sousTotal >= 5000 ? 0 : 490;
        $total     = $sousTotal + $livraison;

        $adresse = [
            'prenom'      => $this->faker->firstName(),
            'nom'         => $this->faker->lastName(),
            'email'       => $this->faker->safeEmail(),
            'telephone'   => $this->faker->phoneNumber(),
            'adresse'     => $this->faker->streetAddress(),
            'complement'  => null,
            'code_postal' => $this->faker->postcode(),
            'ville'       => $this->faker->city(),
            'pays'        => 'FR',
        ];

        return [
            'statut'              => Commande::STATUT_EN_ATTENTE,
            'sous_total'          => $sousTotal,
            'remise'              => 0,
            'livraison'           => $livraison,
            'tva'                 => 0,
            'total'               => $total,
            'adresse_livraison'   => $adresse,
            'adresse_facturation' => $adresse,
            'methode_paiement'    => $this->faker->randomElement(['stripe', 'paypal', 'virement']),
            'statut_paiement'     => Commande::PAIEMENT_EN_ATTENTE,
            'ip_client'           => $this->faker->ipv4(),
        ];
    }

    public function payee(): static
    {
        return $this->state(fn(array $a) => [
            'statut'          => Commande::STATUT_CONFIRMEE,
            'statut_paiement' => Commande::PAIEMENT_PAYE,
            'transaction_id'  => 'txn_' . $this->faker->bothify('??##??##??##'),
            'paye_at'         => now(),
        ]);
    }

    public function expediee(): static
    {
        return $this->payee()->state(fn(array $a) => [
            'statut'       => Commande::STATUT_EXPEDIEE,
            'transporteur' => $this->faker->randomElement(['Colissimo', 'Chronopost', 'DHL']),
            'numero_suivi' => strtoupper($this->faker->bothify('??###########??')),
            'expedie_at'   => now(),
        ]);
    }

    public function livree(): static
    {
        return $this->expediee()->state(fn(array $a) => [
            'statut'   => Commande::STATUT_LIVREE,
            'livre_at' => now(),
        ]);
    }

    public function annulee(): static
    {
        return $this->state(fn(array $a) => [
            'statut' => Commande::STATUT_ANNULEE,
        ]);
    }
}
