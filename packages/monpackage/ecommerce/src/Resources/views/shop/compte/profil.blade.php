@extends('ecommerce::layouts.app')
@section('title', 'Mon profil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👤 Mon profil</h1>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('ecommerce.compte.profil.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            @if(session('succes'))
            <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3 text-sm text-green-700">
                ✓ {{ session('succes') }}
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-primary-dark transition-colors text-sm">
                    Sauvegarder les modifications
                </button>
                <a href="{{ route('ecommerce.compte.commandes') }}"
                   class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl text-sm hover:bg-gray-50">
                    Mes commandes
                </a>
            </div>
        </form>
    </div>

    {{-- Infos compte --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-5">
        <h2 class="font-semibold text-gray-800 mb-3">Informations du compte</h2>
        <div class="text-sm text-gray-600 space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-500">Membre depuis</span>
                <span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Commandes passées</span>
                <span class="font-medium">{{ $user->commandes()->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Sécurité --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-5">
        <h2 class="font-semibold text-gray-800 mb-3">🔒 Sécurité</h2>
        <p class="text-sm text-gray-500 mb-3">Modifiez votre mot de passe via le système d'authentification de votre application.</p>
        @if(Route::has('password.request'))
        <a href="{{ route('password.request') }}"
           class="text-sm text-primary hover:underline">Modifier mon mot de passe →</a>
        @endif
    </div>
</div>
@endsection
