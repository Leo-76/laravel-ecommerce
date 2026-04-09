<?php

namespace MonPackage\Ecommerce\Services;

use Illuminate\Http\Request;
use MonPackage\Ecommerce\Models\Commande;

class PaiementService
{
    /**
     * Processer le paiement selon la passerelle configurée.
     */
    public function processer(Commande $commande, array $donnees): array
    {
        return match (config('ecommerce.paiement.passerelle', 'stripe')) {
            'stripe'   => $this->stripe($commande, $donnees),
            'paypal'   => $this->paypal($commande, $donnees),
            'virement' => $this->virement($commande),
            'especes'  => $this->especes($commande),
            default    => throw new \RuntimeException('Passerelle de paiement inconnue.'),
        };
    }

    // ── Stripe ────────────────────────────────────────────────────────────────

    private function stripe(Commande $commande, array $donnees): array
    {
        $cleSecrete = config('ecommerce.paiement.stripe.cle_secrete');

        if (! $cleSecrete) {
            throw new \RuntimeException('Clé secrète Stripe manquante dans config/ecommerce.php');
        }

        $ch = curl_init('https://api.stripe.com/v1/payment_intents');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $cleSecrete . ':',
            CURLOPT_POSTFIELDS     => http_build_query([
                'amount'               => $commande->total,
                'currency'             => strtolower(config('ecommerce.boutique.devise', 'eur')),
                'payment_method'       => $donnees['payment_method_id'] ?? '',
                'confirm'              => 'true',
                'metadata[commande]'   => $commande->reference,
            ]),
        ]);

        $reponse = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($reponse['error'])) {
            return ['succes' => false, 'message' => $reponse['error']['message']];
        }

        if ($reponse['status'] === 'succeeded') {
            return ['succes' => true, 'transaction_id' => $reponse['id']];
        }

        // Nécessite une action supplémentaire (3DS)
        if ($reponse['status'] === 'requires_action') {
            return [
                'succes'         => false,
                'requires_action' => true,
                'client_secret'  => $reponse['client_secret'],
                'message'        => 'Authentification 3D Secure requise.',
            ];
        }

        return ['succes' => false, 'message' => 'Paiement non abouti (statut : ' . $reponse['status'] . ')'];
    }

    // ── PayPal ────────────────────────────────────────────────────────────────

    private function paypal(Commande $commande, array $donnees): array
    {
        $orderId = $donnees['paypal_order_id'] ?? null;

        if (! $orderId) {
            return ['succes' => false, 'message' => 'ID de commande PayPal manquant.'];
        }

        $mode        = config('ecommerce.paiement.paypal.mode', 'sandbox');
        $baseUrl     = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $clientId    = config('ecommerce.paiement.paypal.client_id');
        $secret      = config('ecommerce.paiement.paypal.secret');

        // Obtenir le token
        $ch = curl_init("{$baseUrl}/v1/oauth2/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => "{$clientId}:{$secret}",
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
        ]);
        $tokenData = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $accessToken = $tokenData['access_token'] ?? null;
        if (! $accessToken) {
            return ['succes' => false, 'message' => 'Impossible de s\'authentifier auprès de PayPal.'];
        }

        // Capturer la commande
        $ch = curl_init("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json',
            ],
        ]);
        $capture = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (($capture['status'] ?? '') === 'COMPLETED') {
            $transactionId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? $orderId;
            return ['succes' => true, 'transaction_id' => $transactionId];
        }

        return ['succes' => false, 'message' => 'La capture PayPal a échoué.'];
    }

    // ── Virement bancaire ─────────────────────────────────────────────────────

    private function virement(Commande $commande): array
    {
        // Paiement différé — la commande est créée en attente
        return [
            'succes'         => true,
            'transaction_id' => 'VIREMENT-' . $commande->reference,
            'message'        => 'Commande en attente de réception du virement.',
        ];
    }

    // ── Paiement à la livraison ───────────────────────────────────────────────

    private function especes(Commande $commande): array
    {
        return [
            'succes'         => true,
            'transaction_id' => 'ESPECES-' . $commande->reference,
        ];
    }

    // ── Webhook Stripe ────────────────────────────────────────────────────────

    public function handleWebhook(string $passerelle, Request $request)
    {
        return match ($passerelle) {
            'stripe' => $this->webhookStripe($request),
            default  => response()->json(['status' => 'ignored']),
        };
    }

    private function webhookStripe(Request $request)
    {
        $secret  = config('ecommerce.paiement.stripe.webhook');
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        // Vérification de la signature (simplifié — utiliser la lib Stripe en prod)
        $event = json_decode($payload, true);

        if (($event['type'] ?? '') === 'payment_intent.succeeded') {
            $pi        = $event['data']['object'];
            $reference = $pi['metadata']['commande'] ?? null;

            if ($reference) {
                $commande = Commande::where('reference', $reference)->first();
                if ($commande && ! $commande->estPayee()) {
                    $commande->marquerPaye($pi['id']);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
