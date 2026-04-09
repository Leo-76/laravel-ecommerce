<?php

namespace MonPackage\Ecommerce\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MonPackage\Ecommerce\Models\Coupon;

class CouponAdminController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('ecommerce::admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('ecommerce::admin.coupons.form');
    }

    public function store(Request $request)
    {
        $r = $this->valider($request);
        $r['code'] = strtoupper($r['code']);
        $r['valeur'] = $r['type'] === 'pourcentage' ? $r['valeur'] : (int)($r['valeur'] * 100);
        if (isset($r['minimum_commande'])) $r['minimum_commande'] = (int)($r['minimum_commande'] * 100);
        $coupon = Coupon::create($r);
        return redirect()->route('ecommerce.admin.coupons.index')->with('succes', "Coupon « {$coupon->code} » créé.");
    }

    public function edit(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('ecommerce::admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $r = $this->valider($request, $id);
        $r['code'] = strtoupper($r['code']);
        $r['valeur'] = $r['type'] === 'pourcentage' ? $r['valeur'] : (int)($r['valeur'] * 100);
        if (isset($r['minimum_commande'])) $r['minimum_commande'] = (int)($r['minimum_commande'] * 100);
        $coupon->update($r);
        return redirect()->route('ecommerce.admin.coupons.index')->with('succes', 'Coupon mis à jour.');
    }

    public function destroy(int $id)
    {
        Coupon::findOrFail($id)->delete();
        return back()->with('succes', 'Coupon supprimé.');
    }

    private function valider(Request $request, ?int $excludeId = null): array
    {
        return $request->validate([
            'code'              => 'required|string|max:50|unique:eco_coupons,code' . ($excludeId ? ",{$excludeId}" : ''),
            'type'              => 'required|in:pourcentage,fixe,livraison',
            'valeur'            => 'required|numeric|min:0',
            'minimum_commande'  => 'nullable|numeric|min:0',
            'utilisations_max'  => 'nullable|integer|min:1',
            'actif'             => 'boolean',
            'debut_at'          => 'nullable|date',
            'fin_at'            => 'nullable|date|after_or_equal:debut_at',
        ]);
    }
}
