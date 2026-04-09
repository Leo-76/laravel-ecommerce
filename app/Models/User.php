<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ecommerce_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'ecommerce_admin'   => 'boolean',
        ];
    }

    // ── Rôles ─────────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super-admin']) || $this->ecommerce_admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function hasRole(string $role): bool
    {
        return match ($role) {
            'ecommerce-admin', 'admin' => $this->isAdmin(),
            'super-admin'              => $this->isSuperAdmin(),
            default                    => false,
        };
    }

    // ── Relations E-commerce ──────────────────────────────────────────────────

    public function commandes(): HasMany
    {
        return $this->hasMany(\MonPackage\Ecommerce\Models\Commande::class, 'user_id');
    }

    public function avis(): HasMany
    {
        return $this->hasMany(\MonPackage\Ecommerce\Models\Avis::class, 'user_id');
    }

    public function wishlist()
    {
        return $this->belongsToMany(
            \MonPackage\Ecommerce\Models\Produit::class,
            'eco_wishlist',
            'user_id',
            'produit_id'
        )->withTimestamps();
    }
}
