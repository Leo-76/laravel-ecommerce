<?php

namespace MonPackage\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use MonPackage\Ecommerce\Models\Produit;
use MonPackage\Ecommerce\Models\Categorie;

class EcommerceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Insertion des données de démonstration e-commerce...');

        // ── Catégories ─────────────────────────────────────────────────────────
        $categories = [
            ['nom' => 'Électronique',    'slug' => 'electronique',  'description' => 'Smartphones, tablettes, accessoires tech'],
            ['nom' => 'Vêtements',       'slug' => 'vetements',     'description' => 'Mode homme, femme, enfant'],
            ['nom' => 'Maison & Jardin', 'slug' => 'maison-jardin', 'description' => 'Décoration, mobilier, jardinerie'],
            ['nom' => 'Sports',          'slug' => 'sports',        'description' => 'Équipements sportifs et loisirs'],
            ['nom' => 'Livres',          'slug' => 'livres',        'description' => 'Romans, BD, essais, manuels'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $c = Categorie::firstOrCreate(['slug' => $cat['slug']], array_merge($cat, ['actif' => true]));
            $catIds[$cat['slug']] = $c->id;
        }

        // ── Produits de démo ───────────────────────────────────────────────────
        $produits = [
            // Électronique
            ['nom' => 'Smartphone Pro X12',      'prix' => 89900, 'stock' => 50,  'cat' => 'electronique', 'description' => 'Smartphone haut de gamme avec écran OLED 6,7", triple caméra 200MP, batterie 5000mAh.'],
            ['nom' => 'Casque Bluetooth ANC',    'prix' => 14990, 'stock' => 120, 'cat' => 'electronique', 'description' => 'Casque à réduction de bruit active, 30h d\'autonomie, son Hi-Fi.', 'prix_promo' => 11990],
            ['nom' => 'Tablette UltraSlim',      'prix' => 49900, 'stock' => 30,  'cat' => 'electronique', 'description' => 'Tablette 10" légère et puissante, idéale pour le travail et les loisirs.'],
            ['nom' => 'Montre Connectée Fit',    'prix' => 19990, 'stock' => 75,  'cat' => 'electronique', 'description' => 'Montre connectée sport, GPS intégré, suivi santé avancé, waterproof 50m.'],
            // Vêtements
            ['nom' => 'T-Shirt Coton Bio',       'prix' =>  2990, 'stock' => 200, 'cat' => 'vetements',    'description' => 'T-shirt 100% coton biologique, disponible en 8 couleurs, coupe classique.'],
            ['nom' => 'Jean Slim Éco',            'prix' =>  5990, 'stock' => 80,  'cat' => 'vetements',    'description' => 'Jean slim en coton recyclé, teinture naturelle, coupe moderne.', 'prix_promo' => 4490],
            ['nom' => 'Veste Outdoor Gore-Tex',  'prix' => 18990, 'stock' => 25,  'cat' => 'vetements',    'description' => 'Veste imperméable 3 couches, légère et respirante, parfaite pour la randonnée.'],
            ['nom' => 'Sneakers Confort +',      'prix' =>  8990, 'stock' => 60,  'cat' => 'vetements',    'description' => 'Baskets ergonomiques avec semelle mémoire de forme, nombreux coloris.'],
            // Maison & Jardin
            ['nom' => 'Lampe Design Bambou',     'prix' =>  4990, 'stock' => 40,  'cat' => 'maison-jardin','description' => 'Lampe de chevet artisanale en bambou certifié, lumière chaude LED incluse.'],
            ['nom' => 'Cafetière à Piston',      'prix' =>  3490, 'stock' => 90,  'cat' => 'maison-jardin','description' => 'French press en verre borosilicaté et acier inox, 1 litre, filtre fin inclus.'],
            ['nom' => 'Plaid Sherpa Géant',      'prix' =>  2990, 'stock' => 150, 'cat' => 'maison-jardin','description' => 'Plaid ultra-doux 200x220cm, lavable machine, look cosy garanti.', 'prix_promo' => 1990],
            ['nom' => 'Kit Jardinage Pro',       'prix' =>  6990, 'stock' => 35,  'cat' => 'maison-jardin','description' => 'Set de 7 outils de jardinage ergonomiques, manche anti-glisse, rangement inclus.'],
            // Sports
            ['nom' => 'Vélo de Route Carbon',   'prix' =>349900, 'stock' => 5,   'cat' => 'sports',       'description' => 'Vélo de route cadre carbone ultra-léger, groupe Shimano 105, roues 700c.'],
            ['nom' => 'Tapis de Yoga Premium',  'prix' =>  4990, 'stock' => 100, 'cat' => 'sports',       'description' => 'Tapis antidérapant 6mm, matière naturelle, sac de transport inclus.'],
            ['nom' => 'Haltères Réglables 20kg','prix' => 12990, 'stock' => 20,  'cat' => 'sports',       'description' => 'Paire d\'haltères réglables de 2 à 20 kg, système de verrouillage rapide.'],
            ['nom' => 'Montre Running GPS',     'prix' => 29990, 'stock' => 45,  'cat' => 'sports',       'description' => 'Montre de running avec GPS précis, cardio optique, 14h d\'autonomie.', 'prix_promo' => 24990],
            // Livres
            ['nom' => 'Laravel Beyond CRUD',    'prix' =>  3990, 'stock' => 500, 'cat' => 'livres',       'description' => 'Guide avancé Laravel pour architectures complexes. Par Brent Roose.'],
            ['nom' => 'Clean Code',             'prix' =>  3490, 'stock' => 300, 'cat' => 'livres',       'description' => 'Le classique de Robert C. Martin sur l\'art d\'écrire du code maintenable.'],
            ['nom' => 'Domain-Driven Design',   'prix' =>  4290, 'stock' => 150, 'cat' => 'livres',       'description' => 'Tackling complexity in the heart of software. Par Eric Evans.'],
            ['nom' => 'The Pragmatic Programmer','prix' => 3690, 'stock' => 200, 'cat' => 'livres',       'description' => '20th anniversary edition — votre voyage vers la maîtrise du développement.'],
        ];

        foreach ($produits as $data) {
            $cat = $data['cat'];
            unset($data['cat']);

            $produit = Produit::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['nom'])],
                array_merge($data, [
                    'actif'      => true,
                    'en_vedette' => rand(0, 3) === 0,
                    'tva'        => 20,
                ])
            );

            if (isset($catIds[$cat])) {
                $produit->categories()->syncWithoutDetaching([$catIds[$cat]]);
            }
        }

        $this->command->info('✅ ' . count($produits) . ' produits et ' . count($categories) . ' catégories créés.');
    }
}
