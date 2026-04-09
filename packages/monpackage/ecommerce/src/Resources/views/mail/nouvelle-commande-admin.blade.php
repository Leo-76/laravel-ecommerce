<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle commande</title>
<style>
    body { margin: 0; padding: 0; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; }
    .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
    .header { background: linear-gradient(135deg, #1e293b, #334155); padding: 32px 40px; }
    .header h1 { color: white; margin: 0; font-size: 20px; font-weight: 700; }
    .header p { color: #94a3b8; margin: 6px 0 0; font-size: 13px; }
    .body { padding: 32px 40px; }
    .kpi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
    .kpi { background: #f8fafc; border-radius: 10px; padding: 14px; }
    .kpi .label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi .value { font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    th { text-align: left; font-size: 11px; color: #94a3b8; text-transform: uppercase; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    td { padding: 10px 0; border-bottom: 1px solid #f8fafc; font-size: 13px; }
    .btn { display: inline-block; background: #2563eb; color: white; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; margin: 16px 0; }
    .footer { background: #f8fafc; padding: 20px 40px; text-align: center; font-size: 11px; color: #94a3b8; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <p>🛒 {{ config('ecommerce.boutique.nom') }}</p>
        <h1>Nouvelle commande reçue !</h1>
        <p>{{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="body">
        <div class="kpi-grid">
            <div class="kpi">
                <div class="label">Référence</div>
                <div class="value" style="font-size:15px; font-family:monospace;">{{ $commande->reference }}</div>
            </div>
            <div class="kpi">
                <div class="label">Total</div>
                <div class="value">{{ number_format($commande->total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</div>
            </div>
            <div class="kpi">
                <div class="label">Client</div>
                <div class="value" style="font-size:14px;">
                    {{ ($commande->adresse_livraison['prenom'] ?? '') . ' ' . ($commande->adresse_livraison['nom'] ?? '') }}
                </div>
            </div>
            <div class="kpi">
                <div class="label">Paiement</div>
                <div class="value" style="font-size:14px; color:#16a34a;">✓ Payée</div>
            </div>
        </div>

        <h3 style="font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">Articles commandés</h3>
        <table>
            <thead><tr>
                <th>Produit</th>
                <th style="text-align:center">Qté</th>
                <th style="text-align:right">Total</th>
            </tr></thead>
            <tbody>
                @foreach($commande->items as $item)
                <tr>
                    <td>{{ $item->nom_produit }}</td>
                    <td style="text-align:center; color:#6b7280;">{{ $item->quantite }}</td>
                    <td style="text-align:right; font-weight:600;">{{ number_format($item->total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="background:#f0fdf4; border-radius:10px; padding:14px; font-size:13px; color:#166534; margin-top:16px;">
            <p><strong>Livraison à :</strong></p>
            <p>{{ ($commande->adresse_livraison['prenom'] ?? '') . ' ' . ($commande->adresse_livraison['nom'] ?? '') }},
               {{ $commande->adresse_livraison['adresse'] ?? '' }},
               {{ ($commande->adresse_livraison['code_postal'] ?? '') . ' ' . ($commande->adresse_livraison['ville'] ?? '') }},
               {{ $commande->adresse_livraison['pays'] ?? '' }}</p>
        </div>

        <div style="text-align:center; margin-top:20px;">
            <a href="{{ url(config('ecommerce.prefix.admin', 'admin/boutique') . '/commandes/' . $commande->reference) }}" class="btn">
                Gérer cette commande →
            </a>
        </div>
    </div>
    <div class="footer">
        <p>{{ config('ecommerce.boutique.nom') }} — Notification automatique</p>
    </div>
</div>
</body>
</html>
