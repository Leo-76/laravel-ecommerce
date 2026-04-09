<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Informations de la boutique
    |--------------------------------------------------------------------------
    */
    'boutique' => [
        'nom'          => env('ECOMMERCE_NOM', 'Ma Boutique'),
        'email'        => env('ECOMMERCE_EMAIL', config('mail.from.address')),
        'telephone'    => env('ECOMMERCE_TEL', ''),
        'devise'       => env('ECOMMERCE_DEVISE', 'EUR'),
        'symbole'      => env('ECOMMERCE_SYMBOLE', '€'),
        'locale_prix'  => env('ECOMMERCE_LOCALE', 'fr_FR'),
        'logo'         => env('ECOMMERCE_LOGO', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Préfixes des routes
    |--------------------------------------------------------------------------
    */
    'prefix' => [
        'shop'  => env('ECOMMERCE_PREFIX_SHOP', 'boutique'),
        'admin' => env('ECOMMERCE_PREFIX_ADMIN', 'admin/boutique'),
        'api'   => env('ECOMMERCE_PREFIX_API', 'ecommerce'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */
    'middleware' => [
        'web'   => ['web'],
        'api'   => ['api', 'auth:sanctum'],
        'admin' => ['web', 'auth', 'ecommerce.admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modèles (overridables)
    |--------------------------------------------------------------------------
    */
    'models' => [
        'produit'    => \MonPackage\Ecommerce\Models\Produit::class,
        'categorie'  => \MonPackage\Ecommerce\Models\Categorie::class,
        'commande'   => \MonPackage\Ecommerce\Models\Commande::class,
        'panier'     => \MonPackage\Ecommerce\Models\Panier::class,
        'avis'       => \MonPackage\Ecommerce\Models\Avis::class,
        'coupon'     => \MonPackage\Ecommerce\Models\Coupon::class,
        'user'       => \App\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Panier
    |--------------------------------------------------------------------------
    */
    'panier' => [
        'driver'      => env('ECOMMERCE_PANIER_DRIVER', 'session'), // session | database | redis
        'ttl'         => env('ECOMMERCE_PANIER_TTL', 10080),         // minutes (7 jours)
        'max_items'   => env('ECOMMERCE_PANIER_MAX', 50),
        'prefix_cle'  => 'ecommerce_panier_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paiement
    |--------------------------------------------------------------------------
    */
    'paiement' => [
        'passerelle' => env('ECOMMERCE_PASSERELLE', 'stripe'), // stripe | paypal | virement | especes

        'stripe' => [
            'cle_publique' => env('STRIPE_KEY'),
            'cle_secrete'  => env('STRIPE_SECRET'),
            'webhook'      => env('STRIPE_WEBHOOK_SECRET'),
        ],

        'paypal' => [
            'client_id'  => env('PAYPAL_CLIENT_ID'),
            'secret'     => env('PAYPAL_SECRET'),
            'mode'       => env('PAYPAL_MODE', 'sandbox'), // sandbox | live
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livraison
    |--------------------------------------------------------------------------
    */
    'livraison' => [
        'gratuite_a_partir_de' => env('ECOMMERCE_LIVRAISON_GRATUITE', 5000), // en centimes (50 €)
        'forfait_defaut'        => env('ECOMMERCE_LIVRAISON_FORFAIT', 490),   // 4,90 €
        'zones' => [
            'france'    => ['nom' => 'France métropolitaine', 'prix' => 490,  'delai' => '2-3 jours'],
            'europe'    => ['nom' => 'Europe',                'prix' => 990,  'delai' => '5-7 jours'],
            'monde'     => ['nom' => 'Monde entier',          'prix' => 1990, 'delai' => '10-15 jours'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stock
    |--------------------------------------------------------------------------
    */
    'stock' => [
        'activer_gestion'  => true,
        'seuil_alerte'     => env('ECOMMERCE_STOCK_ALERTE', 5),
        'permettre_zero'   => false, // autoriser commandes hors stock
    ],

    /*
    |--------------------------------------------------------------------------
    | Images produits
    |--------------------------------------------------------------------------
    */
    'images' => [
        'disque'          => env('ECOMMERCE_IMAGES_DISQUE', 'public'),
        'dossier'         => 'ecommerce/produits',
        'largeur_max'     => 1200,
        'hauteur_max'     => 1200,
        'miniature'       => ['largeur' => 400, 'hauteur' => 400],
        'formats_acceptes' => ['jpg', 'jpeg', 'png', 'webp'],
        'taille_max_mo'   => 5,
        'watermark'       => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Avis clients
    |--------------------------------------------------------------------------
    */
    'avis' => [
        'activer'           => true,
        'moderation'        => true,  // true = approbation manuelle requise
        'achat_requis'      => true,  // uniquement les acheteurs vérifiés
        'note_min'          => 1,
        'note_max'          => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | TVA
    |--------------------------------------------------------------------------
    */
    'tva' => [
        'afficher_ttc'  => true,
        'taux_defaut'   => env('ECOMMERCE_TVA', 20), // 20%
        'incluse_prix'  => true, // les prix sont déjà TTC
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'admin_nouvelle_commande' => true,
        'client_confirmation'     => true,
        'client_expedition'       => true,
        'admin_stock_faible'      => true,
        'canal'                   => ['mail'], // mail | slack | database
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO & Pagination
    |--------------------------------------------------------------------------
    */
    'catalogue' => [
        'produits_par_page' => env('ECOMMERCE_PAR_PAGE', 12),
        'tri_defaut'        => 'nouveautes', // nouveautes | prix_asc | prix_desc | popularite
        'activer_recherche' => true,
        'activer_filtres'   => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Politique de retour
    |--------------------------------------------------------------------------
    */
    'retours' => [
        'activer'          => true,
        'delai_jours'      => 14,
        'motifs'           => [
            'defectueux'   => 'Produit défectueux',
            'mauvaise_size' => 'Mauvaise taille',
            'non_conforme' => 'Non conforme à la description',
            'autre'        => 'Autre raison',
        ],
    ],
];
