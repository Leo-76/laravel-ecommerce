@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
        <a href="{{ route('admin.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Admin</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5 text-left">Utilisateur</th>
                    <th class="px-5 py-3.5 text-center">Rôle</th>
                    <th class="px-5 py-3.5 text-center">Admin boutique</th>
                    <th class="px-5 py-3.5 text-center">Commandes</th>
                    <th class="px-5 py-3.5 text-left">Inscrit le</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold shrink-0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.role', $user) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <select name="role" onchange="this.form.submit()"
                                    class="text-xs border border-gray-200 rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                                <option value="user"        {{ $user->role === 'user'        ? 'selected' : '' }}>Utilisateur</option>
                                <option value="admin"       {{ $user->role === 'admin'       ? 'selected' : '' }}>Admin</option>
                                <option value="super-admin" {{ $user->role === 'super-admin' ? 'selected' : '' }}>Super-Admin</option>
                            </select>
                        </form>
                        @else
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                            {{ ucfirst($user->role) }} (vous)
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.ecommerce-toggle', $user) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs px-3 py-1 rounded-full font-medium transition-colors
                                    {{ $user->ecommerce_admin ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $user->ecommerce_admin ? '✓ Actif' : '○ Inactif' }}
                            </button>
                        </form>
                        @else
                        <span class="text-xs {{ $user->ecommerce_admin ? 'text-orange-600' : 'text-gray-400' }}">
                            {{ $user->ecommerce_admin ? '✓ Actif' : '—' }}
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center font-semibold text-gray-700">{{ $user->commandes_count ?? 0 }}</td>
                    <td class="px-5 py-4 text-xs text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
