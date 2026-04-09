<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmation de commande</title>
<style>
    body { margin: 0; padding: 0; background: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; }
    .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
    .header { background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 40px; text-align: center; }
    .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 700; }
    .header p { color: #bfdbfe; margin: 8px 0 0; font-size: 14px; }
    .body { padding: 40px; }
    .reference { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 30px; }
    .reference p { margin: 0; color: #3b82f6; font-size: 13px; }
    .reference strong { font-size: 22px; color: #1d4ed8; display: block; margin-top: 4px; letter-spacing: 2px; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    table.items th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
    table.items td { padding: 12px 0; border-bottom: 1px solid #f9fafb; font-size: 14px; vertical-align: middle; }
    .totaux { background: #f9fafb; border-radius: 12px; padding: 20px; }
    .totaux div { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: #6b7280; }
    .totaux .total { font-size: 16px; font-weight: 700; color: #111827; border-top: 1px solid #e5e7eb; margin-top: 8px; padding-top: 12px; }
    .footer { background: #f9fafb; padding: 24px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #f3f4f6; }
    .btn { display: inline-block; background: #2563eb; color: white; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; margin: 20px 0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div style="font-size: 40px; margin-bottom: 12px;">🎉</div>
        <h1>Merci pour votre commande !</h1>
        <p>{{ config('ecommerce.boutique.nom') }} — vous avez bien été reçu</p>
    </div>

    <div class="body">
        <div class="reference">
            <p>Numéro de commande</p>
            <strong>{{ $commande->reference }}</strong>
        </div>

        <p style="color: #4b5563; margin-bottom: 24px;">
            Bonjour {{ $commande->adresse_livraison['prenom'] ?? '' }},<br><br>
            Votre commande a bien été reçue et est en cours de traitement.
            Vous recevrez un autre email dès qu'elle sera expédiée.
        </p>

        <table class="items">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th style="text-align:center">Qté</th>
                    <th style="text-align:right">Prix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commande->items as $item)
                <tr>
                    <td>{{ $item->nom_produit }}</td>
                    <td style="text-align:center; color:#6b7280">{{ $item->quantite }}</td>
                    <td style="text-align:right; font-weight:600">{{ number_format($item->total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totaux">
            <div><span>Sous-total</span><span>{{ number_format($commande->sous_total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</span></div>
            @if($commande->remise > 0)
            <div style="color: #16a34a;"><span>Réduction</span><span>−{{ number_format($commande->remise / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</span></div>
            @endif
            <div><span>Livraison</span><span>{{ $commande->livraison === 0 ? 'Gratuite' : number_format($commande->livraison / 100, 2, ',', ' ') . ' ' . config('ecommerce.boutique.symbole') }}</span></div>
            <div class="total"><span>Total</span><span>{{ number_format($commande->total / 100, 2, ',', ' ') }} {{ config('ecommerce.boutique.symbole') }}</span></div>
        </div>

        <div style="text-align:center;">
            <a href="{{ url(config('ecommerce.prefix.shop', 'boutique') . '/compte/commandes/' . $commande->reference) }}" class="btn">
                Suivre ma commande →
            </a>
        </div>

        <div style="background:#f9fafb; border-radius:12px; padding:16px; font-size:13px; color:#6b7280;">
            <strong style="color:#374151;">Adresse de livraison</strong><br>
            {{ $commande->adresse_livraison['prenom'] ?? '' }} {{ $commande->adresse_livraison['nom'] ?? '' }}<br>
            {{ $commande->adresse_livraison['adresse'] ?? '' }}<br>
            @if(!empty($commande->adresse_livraison['complement'])){{ $commande->adresse_livraison['complement'] }}<br>@endif
            {{ $commande->adresse_livraison['code_postal'] ?? '' }} {{ $commande->adresse_livraison['ville'] ?? '' }}<br>
            {{ $commande->adresse_livraison['pays'] ?? '' }}
        </div>
    </div>

    <div class="footer">
        <p>Des questions ? Contactez-nous : <a href="mailto:{{ config('ecommerce.boutique.email') }}" style="color: #3b82f6;">{{ config('ecommerce.boutique.email') }}</a></p>
        <p style="margin-top: 8px;">© {{ date('Y') }} {{ config('ecommerce.boutique.nom') }} — Tous droits réservés</p>
    </div>
</div>
</body>
</html>
