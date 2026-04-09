<?php namespace MonPackage\Ecommerce\Models;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table   = 'eco_coupons';
    protected $guarded = ['id'];
    protected $casts   = ['actif' => 'boolean', 'debut_at' => 'datetime', 'fin_at' => 'datetime'];

    public function scopeActif($query)
    {
        return $query->where('actif', true)
            ->where(fn($q) => $q->whereNull('debut_at')->orWhere('debut_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('fin_at')->orWhere('fin_at', '>=', now()))
            ->where(fn($q) => $q->whereNull('utilisations_max')->orWhereColumn('utilisations_count', '<', 'utilisations_max'));
    }
}
