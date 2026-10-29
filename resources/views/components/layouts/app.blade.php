@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-gray-900 antialiased">
<div class="flex min-h-full flex-col">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-lg font-semibold text-brand-600">{{ config('app.name') }}</a>
                <nav class="hidden items-center gap-4 text-sm sm:flex">
                    <a href="{{ route('home') }}" @class(['hover:text-brand-600', 'font-medium text-brand-600' => request()->routeIs('home', 'jobs.*')])>Offres</a>
                    @auth
                        <a href="{{ route('applications.index') }}" @class(['hover:text-brand-600', 'font-medium text-brand-600' => request()->routeIs('applications.*')])>Mes candidatures</a>
                    @endauth
                </nav>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <livewire:notification-bell />

                    @if (auth()->user()->canAccessPanel(filament()->getDefaultPanel()))
                        <a href="/admin" class="hidden rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50 sm:inline-block">Back-office</a>
                    @endif

                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="" class="h-8 w-8 rounded-full">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-medium text-brand-700">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                            @endif
                            <span class="hidden text-sm sm:inline">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 z-20 mt-2 w-48 rounded-md border border-gray-200 bg-white py-1 shadow-lg">
                            <a href="{{ route('applications.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50 sm:hidden">Mes candidatures</a>
                            @if (auth()->user()->canAccessPanel(filament()->getDefaultPanel()))
                                <a href="/admin" class="block px-4 py-2 text-sm hover:bg-gray-50 sm:hidden">Back-office</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50">Se déconnecter</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-md bg-brand-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-600">Se connecter</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @foreach (['success' => 'border-green-200 bg-green-50 text-green-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $key => $classes)
            @if (session($key))
                <div class="mb-6 rounded-md border px-4 py-3 text-sm {{ $classes }}">{{ session($key) }}</div>
            @endif
        @endforeach

        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200 bg-white py-4 text-center text-xs text-gray-500">
        {{ config('app.name') }} — {{ now()->year }}
    </footer>
</div>
@livewireScripts
</body>
</html>
