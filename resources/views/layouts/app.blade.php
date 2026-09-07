<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div id="loader" class="fixed inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-[9999]">
            <div class="w-12 h-12 border-4 border-gray-300 border-t-primary rounded-full animate-spin mb-4"></div>
            <p class="text-gray-700 font-medium">Carregando...</p>
        </div>
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-transparent">
                    <div class="text-primary mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{-- Titulo da página --}}
                        {{ $header }}

                        {{-- Alertas --}}
                        <div class="mt-4">
                            @foreach (['success', 'error', 'warning', 'info'] as $type)
                                @if(session($type))
                                    <x-alert :type="$type">
                                        {{ session($type) }}
                                    </x-alert>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <div class="py-4 mx-auto sm:px-6 lg:px-8 overflow-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
