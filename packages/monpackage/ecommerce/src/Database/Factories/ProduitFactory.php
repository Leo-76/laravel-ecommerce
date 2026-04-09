<?php

namespace MonPackage\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MonPackage\Ecommerce\Models\Produit;
use Illuminate\Support\Str;

class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    public function definition(): array
    {
        $nom  = $this->faker->unique()->words(rand(2, 4), true);
        $prix = $this->faker->numberBetween(500, 99900); // centimes

        return [
            'nom'               => ucfirst($nom),
            'slug'              => Str::slug($nom),
            'sku'               => 'SKU-' . strtoupper($this->faker->bothify('??####')),
            'description_courte'=> $this->faker->sentence(10),
            'description'       => $this->faker->paragraphs(3, true),
            'prix'              => $prix,
            'prix_promo'        => null,
            'stock'             => $this->faker->numberBetween(0, 200),
            'tva'               => $this->faker->randomElement([0, 5, 10, 20]),
            'actif'             => true,
            'en_vedette'        => $this->faker->boolean(20),
            'numerique'         => false,
            'poids'             => $this->faker->optional()->randomFloat(3, 0.1, 20),
            'ventes_total'      => $this->faker->numberBetween(0, 500),
        ];
    }

    /** Produit actif avec stock */
    public function enStock(): static
    {
        return $this->state(fn(array $a) => [
            'actif' => true,
            'stock' => $this->faker->numberBetween(5, 100),
        ]);
    }

    /** Produit en rupture */
    public function rupture(): static
    {
        return $this->state(fn(array $a) => ['stock' => 0]);
    }

    /** Produit en promotion */
    public function enPromo(): static
    {
        return $this->state(function (array $a) {
            $promo = (int) ($a['prix'] * $this->faker->randomFloat(2, 0.5, 0.8));
            return [
                'prix_promo'  => $promo,
                'promo_debut' => now()->subDays(5),
                'promo_fin'   => now()->addDays(10),
            ];
        });
    }

    /** Produit en vedette */
    public function vedette(): static
    {
        return $this->state(fn(array $a) => ['en_vedette' => true]);
    }

    /** Produit inactif */
    public function inactif(): static
    {
        return $this->state(fn(array $a) => ['actif' => false]);
    }

    /** Produit numérique (sans stock) */
    public function numerique(): static
    {
        return $this->state(fn(array $a) => [
            'numerique' => true,
            'stock'     => 9999,
            'poids'     => null,
        ]);
    }
}
