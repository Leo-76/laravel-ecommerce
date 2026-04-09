@extends('ecommerce::layouts.admin')
@section('title', 'Paramètres')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-sm text-blue-700 flex gap-3">
        <span class="text-xl shrink-0">ℹ️</span>
        <div>
            <p class="font-semibold mb-1">Configuration via fichier ou .env</p>
            <p>Les paramètres ci-dessous reflètent votre <code class="font-mono bg-blue-100 px-1 rounded">config/ecommerce.php</code>.
            Pour les modifier de façon permanente, éditez ce fichier ou les variables d'environnement correspondantes dans votre <code class="font-mono bg-blue-100 px-1 rounded">.env</code>.</p>
        </div>
    </div>

    {{-- Boutique --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-3">🏪 Informations boutique</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Nom :</span> <span class="font-medium">{{ config('ecommerce.boutique.nom') }}</span></div>
            <div><span class="text-gray-500">Email :</span> <span class="font-medium">{{ config('ecommerce.boutique.email') }}</span></div>
            <div><span class="text-gray-500">Devise :</span> <span class="font-medium">{{ config('ecommerce.boutique.devise') }} ({{ config('ecommerce.boutique.symbole') }})</span></div>
            <div><span class="text-gray-500">Locale prix :</span> <span class="font-medium">{{ config('ecommerce.boutique.locale_prix') }}</span></div>
        </div>
        <div class="pt-2 text-xs text-gray-400 font-mono bg-gray-50 rounded-xl px-4 py-3 space-y-1">
            <p>ECOMMERCE_NOM="{{ config('ecommerce.boutique.nom') }}"</p>
            <p>ECOMMERCE_EMAIL="{{ config('ecommerce.boutique.email') }}"</p>
            <p>ECOMMERCE_DEVISE="{{ config('ecommerce.boutique.devise') }}"</p>
        </div>
    </div>

    {{-- Paiement --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-3">💳 Paiement</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Passerelle active :</span>
                <span class="font-bold text-blue-600 ml-2 capitalize">{{ config('ecommerce.paiement.passerelle') }}</span>
            </div>
        </div>
        <div class="space-y-2">
            @foreach(['stripe', 'paypal'] as $pg)
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                <span class="font-medium text-sm capitalize">{{ $pg }}</span>
                @php $ok = (bool) config("ecommerce.paiement.{$pg}.cle_secrete"); @endphp
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $ok ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                    {{ $ok ? '✓ Configuré' : '✗ Non configuré' }}
                </span>
            </div>
            @endforeach
        </div>
        <div class="text-xs text-gray-400 font-mono bg-gray-50 rounded-xl px-4 py-3 space-y-1">
            <p>ECOMMERCE_PASSERELLE="{{ config('ecommerce.paiement.passerelle') }}"</p>
            <p>STRIPE_KEY="..."</p>
            <p>STRIPE_SECRET="..."</p>
        </div>
    </div>

    {{-- Livraison --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-3">🚚 Livraison</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Livraison gratuite dès :</span>
                <span class="font-medium ml-2">@prixFormate(config('ecommerce.livraison.gratuite_a_partir_de', 5000))</span>
            </div>
            <div>
                <span class="text-gray-500">Forfait livraison :</span>
                <span class="font-medium ml-2">@prixFormate(config('ecommerce.livraison.forfait_defaut', 490))</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-gray-600">
                <thead><tr class="text-gray-400 border-b border-gray-100">
                    <th class="text-left pb-2">Zone</th><th class="text-right pb-2">Prix</th><th class="text-right pb-2">Délai</th>
                </tr></thead>
                <tbody>
                    @foreach(config('ecommerce.livraison.zones', []) as $zone)
                    <tr class="border-b border-gray-50">
                        <td class="py-2">{{ $zone['nom'] }}</td>
                        <td class="py-2 text-right font-medium">@prixFormate($zone['prix'])</td>
                        <td class="py-2 text-right text-gray-400">{{ $zone['delai'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stock & TVA --}}
    <div class="grid grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
            <h2 class="font-bold text-gray-800">📦 Stock</h2>
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Gestion stock :</span>
                    <span class="{{ config('ecommerce.stock.activer_gestion') ? 'text-green-600' : 'text-gray-400' }} font-medium">
                        {{ config('ecommerce.stock.activer_gestion') ? '✓ Activée' : '✗ Désactivée' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Seuil alerte :</span>
                    <span class="font-medium">{{ config('ecommerce.stock.seuil_alerte') }} unités</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Vente hors stock :</span>
                    <span class="{{ config('ecommerce.stock.permettre_zero') ? 'text-orange-500' : 'text-green-600' }} font-medium">
                        {{ config('ecommerce.stock.permettre_zero') ? 'Autorisée' : 'Bloquée' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
            <h2 class="font-bold text-gray-800">🧾 TVA</h2>
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Affichage TTC :</span>
                    <span class="font-medium">{{ config('ecommerce.tva.afficher_ttc') ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Taux par défaut :</span>
                    <span class="font-bold">{{ config('ecommerce.tva.taux_defaut') }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Prix inclus TVA :</span>
                    <span class="font-medium">{{ config('ecommerce.tva.incluse_prix') ? 'Oui (TTC)' : 'Non (HT)' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Commandes rapides --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4">⚡ Commandes Artisan utiles</h2>
        <div class="space-y-2 font-mono text-xs text-gray-600 bg-gray-900 rounded-xl p-4">
            <p class="text-gray-400"># Re-publier la config</p>
            <p class="text-green-400">php artisan vendor:publish --tag=ecommerce-config --force</p>
            <p class="text-gray-400 mt-2"># Re-publier les vues</p>
            <p class="text-green-400">php artisan vendor:publish --tag=ecommerce-views --force</p>
            <p class="text-gray-400 mt-2"># Créer un admin</p>
            <p class="text-green-400">php artisan e-commerce:make-admin email@exemple.com</p>
            <p class="text-gray-400 mt-2"># Insérer les données de démo</p>
            <p class="text-green-400">php artisan db:seed --class="MonPackage\Ecommerce\Database\Seeders\EcommerceDemoSeeder"</p>
        </div>
    </div>
</div>
@endsection
