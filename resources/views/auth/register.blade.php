<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
<div class="w-full max-w-md px-4">

    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">🛒 {{ config('app.name') }}</a>
        <p class="text-gray-500 text-sm mt-1">Créez votre compte</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white font-medium py-2.5 rounded-xl hover:bg-blue-700 transition-colors text-sm">
                Créer mon compte
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-gray-500 mt-4">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Se connecter</a>
    </p>
</div>
</body>
</html>
