<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Alerte stock faible</title>
<style>
    body { margin: 0; padding: 0; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; }
    .wrapper { max-width: 540px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
    .header { background: linear-gradient(135deg, #f59e0b, #d97706); padding: 32px 40px; text-align: center; }
    .header h1 { color: white; margin: 0; font-size: 20px; font-weight: 700; }
    .body { padding: 32px 40px; }
    .produit-card { background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 20px; margin: 16px 0; }
    .btn { display: inline-block; background: #d97706; color: white; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; }
    .footer { background: #f9fafb; padding: 16px 40px; text-align: center; font-size: 11px; color: #9ca3af; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div style="font-size:40px; margin-bottom:8px;">⚠️</div>
        <h1>Alerte stock faible</h1>
        <p style="color:#fef3c7; font-size:13px; margin:6px 0 0;">{{ config('ecommerce.boutique.nom') }}</p>
    </div>
    <div class="body">
        <p style="color:#4b5563; margin-bottom:16px;">
            Le stock d'un de vos produits est passé sous le seuil d'alerte de
            <strong>{{ config('ecommerce.stock.seuil_alerte', 5) }} unités</strong>.
        </p>

        <div class="produit-card">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <p style="font-weight:700; font-size:16px; color:#1f2937; margin:0;">{{ $produit->nom }}</p>
                    @if($produit->sku)
                    <p style="font-family:monospace; font-size:12px; color:#9ca3af; margin:4px 0 0;">SKU : {{ $produit->sku }}</p>
                    @endif
                </div>
                <div style="text-align:right;">
                    <div style="font-size:28px; font-weight:800; color:{{ $produit->stock === 0 ? '#dc2626' : '#d97706' }};">
                        {{ $produit->stock }}
                    </div>
                    <div style="font-size:12px; color:#6b7280;">unité(s) restante(s)</div>
                </div>
            </div>
            @if($produit->categories->isNotEmpty())
            <p style="font-size:12px; color:#6b7280; margin:12px 0 0;">
                Catégories : {{ $produit->categories->pluck('nom')->implode(', ') }}
            </p>
            @endif
        </div>

        @if($produit->stock === 0)
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; font-size:13px; color:#991b1b; margin-bottom:16px;">
            🚨 <strong>Ce produit est en rupture totale de stock.</strong>
            Il est actuellement invisible pour les clients.
        </div>
        @endif

        <div style="text-align:center; margin-top:20px;">
            <a href="{{ url(config('ecommerce.prefix.admin', 'admin/boutique') . '/produits/' . $produit->id . '/edit') }}" class="btn">
                Mettre à jour le stock →
            </a>
        </div>
    </div>
    <div class="footer">
        <p>{{ config('ecommerce.boutique.nom') }} — Alerte automatique</p>
        <p>Seuil d'alerte configuré à {{ config('ecommerce.stock.seuil_alerte', 5) }} unités dans config/ecommerce.php</p>
    </div>
</div>
</body>
</html>
