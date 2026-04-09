<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Ecommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center">

    <div class="text-center max-w-2xl px-4">
        <div class="text-6xl mb-6">🛒</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-3">Laravel Ecommerce</h1>
        <p class="text-gray-500 text-lg mb-8">Application e-commerce complète — boutique, panier, commandes, panel admin.</p>

        <div class="flex flex-wrap gap-3 justify-center mb-12">
            @auth
            <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-blue-700 transition-colors">
                Dashboard →
            </a>
            <a href="{{ route('ecommerce.home') }}" class="bg-gray-800 text-white px-6 py-3 rounded-xl font-medium hover:bg-gray-700 transition-colors">
                Boutique →
            </a>
            @else
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-blue-700 transition-colors">
                Connexion
            </a>
            <a href="{{ route('register') }}" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                S'inscrire
            </a>
            <a href="{{ route('ecommerce.home') }}" class="bg-gray-800 text-white px-6 py-3 rounded-xl font-medium hover:bg-gray-700 transition-colors">
                Voir la boutique
            </a>
            @endauth
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            @foreach([
                ['🏷️', 'Catalogue produits', '/boutique/produits'],
                ['🛒', 'Panier & Checkout', '/boutique/panier'],
                ['📦', 'Mes commandes', '/boutique/compte/commandes'],
                ['⚙️', 'Panel admin', '/admin/boutique'],
            ] as [$icon, $label, $url])
            <a href="{{ $url }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-sm transition-all text-center">
                <div class="text-2xl mb-2">{{ $icon }}</div>
                <div class="text-gray-700 font-medium">{{ $label }}</div>
            </a>
            @endforeach
        </div>
    </div>

    <div class="mt-12 text-xs text-gray-400">
        Laravel {{ app()->version() }} · monpackage/ecommerce v1.0
    </div>

</body>
</html>
