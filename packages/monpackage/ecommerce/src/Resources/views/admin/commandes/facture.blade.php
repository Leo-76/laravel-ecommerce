<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $commande->reference }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 13px; color: #1f2937; background: white; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 24px; border-bottom: 2px solid #2563eb; }
        .logo { font-size: 22px; font-weight: 800; color: #2563eb; }
        .boutique-info { text-align: right; color: #6b7280; font-size: 12px; line-height: 1.6; }
        .facture-title { font-size: 28px; font-weight: 800; color: #111827; margin-bottom: 4px; }
        .facture-ref { color: #6b7280; font-size: 13px; }
        .section { margin-bottom: 32px; }
        .section-title { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .addresses { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .address-box { background: #f9fafb; border-radius: 10px; padding: 16px; line-height: 1.7; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f3f4f6; }
        th { text-align: left; padding: 10px 14px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; }
        td { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totaux { margin-left: auto; width: 280px; }
        .totaux-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #6b7280; }
        .totaux-total { display: flex; justify-content: space-between; padding: 10px 0; font-size: 16px; font-weight: 800; color: #111827; border-top: 2px solid #e5e7eb; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .footer { margin-top: 48px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 11px; color: #9ca3af; line-height: 1.8; }
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:20px; display:flex; gap:10px;">
    <button onclick="window.print()" style="background:#2563eb;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:600;">🖨 Imprimer / PDF</button>
    <button onclick="window.close()" style="background:#f3f4f6;color:#374151;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;">Fermer</button>
</div>

<div class="header">
    <div>
        <div class="logo">🛒 {{ config('ecommerce.boutique.nom') }}</div>
        <div style="margin-top:4px; color:#6b7280; font-size:12px;">{{ config('ecommerce.boutique.email') }}</div>
        @if(config('ecommerce.boutique.telephone'))
        <div style="color:#6b7280; font-size:12px;">{{ config('ecommerce.boutique.telephone') }}</div>
        @endif
    </div>
    <div class="boutique-info">
        <div class="facture-title">FACTURE</div>
        <div class="facture-ref">N° {{ $commande->reference }}</div>
        <div style="margin-top:4px;">Date : {{ $commande->created_at->format('d/m/Y') }}</div>
        @if($commande->paye_at)
        <div>Payé le : {{ $commande->paye_at->format('d/m/Y') }}</div>
        @endif
        <div style="margin-top:6px;">
            <span class="badge badge-green">✓ Payée</span>
        </div>
    </div>
</div>

{{-- Adresses --}}
<div class="section">
    <div class="addresses">
        <div>
            <div class="section-title">Facturation</div>
            <div class="address-box">
                @php $fac = $commande->adresse_facturation; @endphp
                <strong>{{ ($fac['prenom'] ?? '') . ' ' . ($fac['nom'] ?? '') }}</strong><br>
                {{ $fac['adresse'] ?? '' }}<br>
                @if(!empty($fac['complement'])){{ $fac['complement'] }}<br>@endif
                {{ ($fac['code_postal'] ?? '') . ' ' . ($fac['ville'] ?? '') }}<br>
                {{ $fac['pays'] ?? '' }}<br>
                @if(!empty($fac['email']))<span style="color:#6b7280;">{{ $fac['email'] }}</span>@endif
            </div>
        </div>
        <div>
            <div class="section-title">Livraison</div>
            <div class="address-box">
                @php $liv = $commande->adresse_livraison; @endphp
                <strong>{{ ($liv['prenom'] ?? '') . ' ' . ($liv['nom'] ?? '') }}</strong><br>
                {{ $liv['adresse'] ?? '' }}<br>
                @if(!empty($liv['complement'])){{ $liv['complement'] }}<br>@endif
                {{ ($liv['code_postal'] ?? '') . ' ' . ($liv['ville'] ?? '') }}<br>
                {{ $liv['pays'] ?? '' }}
                @if($commande->numero_suivi)
                <br><span style="color:#2563eb; font-size:11px;">Suivi : {{ $commande->numero_suivi }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Articles --}}
<div class="section">
    <div class="section-title">Détail de la commande</div>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Réf.</th>
                <th class="text-center">Qté</th>
                <th class="text-right">Prix unitaire HT</th>
                <th class="text-center">TVA</th>
                <th class="text-right">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
            <tr>
                <td><strong>{{ $item->nom_produit }}</strong></td>
                <td style="color:#9ca3af; font-family:monospace; font-size:11px;">{{ $item->sku_produit ?? '—' }}</td>
                <td class="text-center">{{ $item->quantite }}</td>
                <td class="text-right">
                    @php
                        $tva = $item->tva ?? 20;
                        $ht = $tva > 0 ? round($item->prix_unitaire / (1 + $tva / 100)) : $item->prix_unitaire;
                    @endphp
                    {{ number_format($ht / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}
                </td>
                <td class="text-center">{{ $item->tva ?? 20 }}%</td>
                <td class="text-right"><strong>{{ number_format($item->total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Totaux --}}
<div class="totaux">
    <div class="totaux-row">
        <span>Sous-total TTC</span>
        <span>{{ number_format($commande->sous_total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</span>
    </div>
    @if($commande->remise > 0)
    <div class="totaux-row" style="color:#16a34a;">
        <span>Réduction ({{ $commande->coupon_code }})</span>
        <span>−{{ number_format($commande->remise / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</span>
    </div>
    @endif
    <div class="totaux-row">
        <span>Livraison</span>
        <span>{{ $commande->livraison === 0 ? 'Offerte' : number_format($commande->livraison / 100, 2, ',', ' ') . ' ' . config('ecommerce.boutique.symbole') }}</span>
    </div>
    <div class="totaux-total">
        <span>TOTAL TTC</span>
        <span>{{ number_format($commande->total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</span>
    </div>
</div>

{{-- Paiement --}}
<div class="section" style="margin-top:32px;">
    <div class="section-title">Informations de paiement</div>
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px; font-size:12px; color:#166534;">
        ✓ <strong>Paiement reçu</strong>
        — Méthode : {{ ucfirst($commande->methode_paiement ?? '—') }}
        @if($commande->transaction_id)
        — Réf. transaction : <span style="font-family:monospace;">{{ $commande->transaction_id }}</span>
        @endif
        @if($commande->paye_at)
        — Le {{ $commande->paye_at->format('d/m/Y à H:i') }}
        @endif
    </div>
</div>

<div class="footer">
    <p><strong>{{ config('ecommerce.boutique.nom') }}</strong></p>
    <p>{{ config('ecommerce.boutique.email') }}
        @if(config('ecommerce.boutique.telephone')) | {{ config('ecommerce.boutique.telephone') }}@endif
    </p>
    <p style="margin-top:8px;">Merci pour votre commande ! Politique de retour : {{ config('ecommerce.retours.delai_jours', 14) }} jours.</p>
    <p style="margin-top:4px; color:#d1d5db;">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
</div>

</body>
</html>
