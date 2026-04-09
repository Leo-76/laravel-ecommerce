<?php

namespace MonPackage\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MonPackage\Ecommerce\Traits\APrix;

class Commande extends Model
{
    use SoftDeletes, APrix;

    protected $table   = 'eco_commandes';
    protected $guarded = ['id'];

    protected $casts = [
        'adresse_livraison'  => 'array',
        'adresse_facturation' => 'array',
        'paye_at'            => 'datetime',
        'expedie_at'         => 'datetime',
        'livre_at'           => 'datetime',
    ];

    // Statuts possibles
    const STATUT_EN_ATTENTE   = 'en_attente';
    const STATUT_CONFIRMEE    = 'confirmee';
    const STATUT_EN_COURS     = 'en_cours';
    const STATUT_EXPEDIEE     = 'expediee';
    const STATUT_LIVREE       = 'livree';
    const STATUT_ANNULEE      = 'annulee';
    const STATUT_REMBOURSEE   = 'remboursee';

    const PAIEMENT_EN_ATTENTE = 'en_attente';
    const PAIEMENT_PAYE       = 'paye';
    const PAIEMENT_ECHEC      = 'echec';
    const PAIEMENT_REMBOURSE  = 'rembourse';

    // ── Relations ─────────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(CommandeItem::class, 'commande_id');
    }

    public function historique(): HasMany
    {
        return $this->hasMany(CommandeHistorique::class, 'commande_id')->latest();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(config('ecommerce.models.user'), 'user_id');
    }

    public function retours(): HasMany
    {
        return $this->hasMany(Retour::class, 'commande_id');
    }

    // ── Accesseurs ────────────────────────────────────────────────────────────

    public function getTotalFormateAttribute(): string
    {
        return $this->formaterPrix($this->total);
    }

    public function getStatutLibelleAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_CONFIRMEE  => 'Confirmée',
            self::STATUT_EN_COURS   => 'En cours de préparation',
            self::STATUT_EXPEDIEE   => 'Expédiée',
            self::STATUT_LIVREE     => 'Livrée',
            self::STATUT_ANNULEE    => 'Annulée',
            self::STATUT_REMBOURSEE => 'Remboursée',
            default                 => ucfirst($this->statut),
        };
    }

    public function getStatutCouleurAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_CONFIRMEE  => 'info',
            self::STATUT_EN_COURS   => 'info',
            self::STATUT_EXPEDIEE   => 'primary',
            self::STATUT_LIVREE     => 'success',
            self::STATUT_ANNULEE    => 'danger',
            self::STATUT_REMBOURSEE => 'secondary',
            default                 => 'secondary',
        };
    }

    // ── Méthodes ─────────────────────────────────────────────────────────────

    public function changerStatut(string $nouveauStatut, ?string $commentaire = null, $userId = null): void
    {
        $ancienStatut = $this->statut;

        $this->update(['statut' => $nouveauStatut]);

        $this->historique()->create([
            'statut_avant'  => $ancienStatut,
            'statut_apres'  => $nouveauStatut,
            'commentaire'   => $commentaire,
            'user_id'       => $userId ?? auth()->id(),
        ]);
    }

    public function marquerPaye(string $transactionId): void
    {
        $this->update([
            'statut_paiement' => self::PAIEMENT_PAYE,
            'transaction_id'  => $transactionId,
            'paye_at'         => now(),
        ]);
        $this->changerStatut(self::STATUT_CONFIRMEE, 'Paiement reçu');
    }

    public function expedier(string $transporteur, string $numeroSuivi): void
    {
        $this->update([
            'transporteur'    => $transporteur,
            'numero_suivi'    => $numeroSuivi,
            'expedie_at'      => now(),
        ]);
        $this->changerStatut(self::STATUT_EXPEDIEE, "Expédiée via {$transporteur} — Suivi : {$numeroSuivi}");
    }

    public function estPayee(): bool
    {
        return $this->statut_paiement === self::PAIEMENT_PAYE;
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [self::STATUT_EN_ATTENTE, self::STATUT_CONFIRMEE]);
    }

    // ── Boot ─────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $commande) {
            if (empty($commande->reference)) {
                $commande->reference = static::genererReference();
            }
        });
    }

    public static function genererReference(): string
    {
        $annee = date('Y');
        $dernier = static::whereYear('created_at', $annee)->max('id') ?? 0;
        $numero = str_pad($dernier + 1, 6, '0', STR_PAD_LEFT);
        return "ECO-{$annee}-{$numero}";
    }
}
