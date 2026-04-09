<?php
// ─── 2024_01_01_000001_creer_tables_ecommerce.php ───────────────────────────

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Catégories ────────────────────────────────────────────────────────
        Schema::create('eco_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('eco_categories')->nullOnDelete();
            $table->integer('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->string('meta_titre')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Produits ──────────────────────────────────────────────────────────
        Schema::create('eco_produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->text('description_courte')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('prix');               // en centimes
            $table->unsignedBigInteger('prix_promo')->nullable();
            $table->date('promo_debut')->nullable();
            $table->date('promo_fin')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedTinyInteger('tva')->default(20);  // %
            $table->string('image_principale')->nullable();
            $table->boolean('actif')->default(true);
            $table->boolean('en_vedette')->default(false);
            $table->boolean('numerique')->default(false);     // produit téléchargeable
            $table->decimal('poids', 8, 3)->nullable();       // kg
            $table->string('unite_poids', 5)->default('kg');
            $table->string('type', 30)->default('simple');   // simple | variable | groupe
            $table->unsignedBigInteger('ventes_total')->default(0);
            $table->string('meta_titre')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Pivot Produit <-> Catégorie ───────────────────────────────────────
        Schema::create('eco_produit_categorie', function (Blueprint $table) {
            $table->foreignId('produit_id')->constrained('eco_produits')->cascadeOnDelete();
            $table->foreignId('categorie_id')->constrained('eco_categories')->cascadeOnDelete();
            $table->primary(['produit_id', 'categorie_id']);
        });

        // ── Images des produits ───────────────────────────────────────────────
        Schema::create('eco_produit_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('eco_produits')->cascadeOnDelete();
            $table->string('chemin');
            $table->string('alt')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // ── Attributs & variations ────────────────────────────────────────────
        Schema::create('eco_attributs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');       // Taille, Couleur, Matière...
            $table->string('slug')->unique();
            $table->boolean('filtrable')->default(true);
            $table->timestamps();
        });

        Schema::create('eco_attribut_valeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribut_id')->constrained('eco_attributs')->cascadeOnDelete();
            $table->string('valeur');    // XL, Rouge, Coton...
            $table->string('couleur_hex', 7)->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('eco_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('eco_produits')->cascadeOnDelete();
            $table->string('sku')->unique()->nullable();
            $table->unsignedBigInteger('prix')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('eco_variation_attributs', function (Blueprint $table) {
            $table->foreignId('variation_id')->constrained('eco_variations')->cascadeOnDelete();
            $table->foreignId('valeur_id')->constrained('eco_attribut_valeurs')->cascadeOnDelete();
            $table->primary(['variation_id', 'valeur_id']);
        });

        // ── Paniers (persistants en base) ─────────────────────────────────────
        Schema::create('eco_paniers', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable();
            $table->unsignedBigInteger('remise')->default(0); // centimes
            $table->timestamps();
            $table->timestamp('expire_at')->nullable();
        });

        Schema::create('eco_panier_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panier_id')->constrained('eco_paniers')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('eco_produits')->cascadeOnDelete();
            $table->foreignId('variation_id')->nullable()->constrained('eco_variations')->nullOnDelete();
            $table->unsignedInteger('quantite')->default(1);
            $table->unsignedBigInteger('prix_unitaire'); // centimes au moment de l'ajout
            $table->json('options')->nullable();
            $table->timestamps();
        });

        // ── Coupons de réduction ──────────────────────────────────────────────
        Schema::create('eco_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('pourcentage'); // pourcentage | fixe | livraison
            $table->unsignedBigInteger('valeur');           // % ou centimes
            $table->unsignedBigInteger('minimum_commande')->nullable();
            $table->unsignedInteger('utilisations_max')->nullable();
            $table->unsignedInteger('utilisations_count')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamp('debut_at')->nullable();
            $table->timestamp('fin_at')->nullable();
            $table->timestamps();
        });

        // ── Commandes ────────────────────────────────────────────────────────
        Schema::create('eco_commandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // ECO-2024-000001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('statut')->default('en_attente');
            // Montants en centimes
            $table->unsignedBigInteger('sous_total');
            $table->unsignedBigInteger('remise')->default(0);
            $table->unsignedBigInteger('livraison');
            $table->unsignedBigInteger('tva');
            $table->unsignedBigInteger('total');
            // Adresses (snapshot au moment de la commande)
            $table->json('adresse_livraison');
            $table->json('adresse_facturation');
            // Paiement
            $table->string('methode_paiement')->nullable();
            $table->string('statut_paiement')->default('en_attente');
            $table->string('transaction_id')->nullable();
            $table->timestamp('paye_at')->nullable();
            // Livraison
            $table->string('transporteur')->nullable();
            $table->string('numero_suivi')->nullable();
            $table->timestamp('expedie_at')->nullable();
            $table->timestamp('livre_at')->nullable();
            // Coupon
            $table->string('coupon_code')->nullable();
            // Divers
            $table->text('notes_client')->nullable();
            $table->text('notes_admin')->nullable();
            $table->string('ip_client')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('eco_commande_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('eco_commandes')->cascadeOnDelete();
            $table->foreignId('produit_id')->nullable()->constrained('eco_produits')->nullOnDelete();
            $table->foreignId('variation_id')->nullable()->constrained('eco_variations')->nullOnDelete();
            $table->string('nom_produit');
            $table->string('sku_produit')->nullable();
            $table->unsignedInteger('quantite');
            $table->unsignedBigInteger('prix_unitaire'); // centimes
            $table->unsignedBigInteger('total');
            $table->unsignedTinyInteger('tva')->default(20);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        // ── Historique des statuts de commande ────────────────────────────────
        Schema::create('eco_commande_historique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('eco_commandes')->cascadeOnDelete();
            $table->string('statut_avant')->nullable();
            $table->string('statut_apres');
            $table->text('commentaire')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // ── Avis clients ──────────────────────────────────────────────────────
        Schema::create('eco_avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('eco_produits')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('auteur_nom');
            $table->string('auteur_email');
            $table->unsignedTinyInteger('note'); // 1-5
            $table->string('titre')->nullable();
            $table->text('contenu')->nullable();
            $table->boolean('approuve')->default(false);
            $table->boolean('achat_verifie')->default(false);
            $table->timestamps();
        });

        // ── Wishlist ──────────────────────────────────────────────────────────
        Schema::create('eco_wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('eco_produits')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'produit_id']);
        });

        // ── Retours ───────────────────────────────────────────────────────────
        Schema::create('eco_retours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('eco_commandes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('statut')->default('demande'); // demande | approuve | refuse | rembourse
            $table->string('motif');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('montant_rembourse')->nullable();
            $table->timestamp('rembourse_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eco_retours');
        Schema::dropIfExists('eco_wishlist');
        Schema::dropIfExists('eco_avis');
        Schema::dropIfExists('eco_commande_historique');
        Schema::dropIfExists('eco_commande_items');
        Schema::dropIfExists('eco_commandes');
        Schema::dropIfExists('eco_coupons');
        Schema::dropIfExists('eco_panier_items');
        Schema::dropIfExists('eco_paniers');
        Schema::dropIfExists('eco_variation_attributs');
        Schema::dropIfExists('eco_variations');
        Schema::dropIfExists('eco_attribut_valeurs');
        Schema::dropIfExists('eco_attributs');
        Schema::dropIfExists('eco_produit_images');
        Schema::dropIfExists('eco_produit_categorie');
        Schema::dropIfExists('eco_produits');
        Schema::dropIfExists('eco_categories');
    }
};
