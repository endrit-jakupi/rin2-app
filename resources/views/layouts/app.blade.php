<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'RIN2') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900">
    <header class="bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-8 border-b border-gray-500">
            @auth
                <nav class="flex items-center gap-6">
                    <a href="/" class="text-sm font-medium no-underline hover:text-gray-600">
                        Home
                    </a>

                    <a href="/users" class="text-sm font-medium no-underline hover:text-gray-600">
                        Users
                    </a>

                    <a href="/notifications" class="text-sm font-medium no-underline hover:text-gray-600">
                        Notifications
                    </a>

                    <a href="/settings" class="text-sm font-medium no-underline hover:text-gray-600">
                        Settings
                    </a>

                    <a href="/notification-management" class="text-sm font-medium no-underline hover:text-gray-600">
                        Notification Management
                    </a>
                </nav>

                <div class="flex items-center gap-6">
                    <span class="text-sm text-gray-600">
                        Logged in as: {{ auth()->user()->name }}
                    </span>

                    @if (auth()->user()->notifications_enabled)
                        <livewire:notification-bell />
                    @endif
                </div>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">
        {{ $slot }}
    </main>
</body>
</html>