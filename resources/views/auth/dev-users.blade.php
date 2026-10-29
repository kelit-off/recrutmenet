<x-layouts.app title="Connexion de développement">
    <div class="mx-auto max-w-2xl">
        <h1 class="text-xl font-semibold">Connexion de développement</h1>
        <p class="mt-1 text-sm text-gray-600">Disponible uniquement quand <code>APP_ENV=local</code>.</p>

        <ul class="mt-6 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white">
            @forelse ($users as $user)
                <li class="flex items-center justify-between px-4 py-3">
                    <div>
                        <p class="font-medium">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->roles->pluck('name')->join(', ') ?: 'Aucun rôle' }}</p>
                    </div>
                    <a href="{{ route('dev.login', $user) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Se connecter</a>
                </li>
            @empty
                <li class="px-4 py-3 text-sm text-gray-500">Aucun utilisateur. Lancez <code>php artisan db:seed</code>.</li>
            @endforelse
        </ul>
    </div>
</x-layouts.app>
