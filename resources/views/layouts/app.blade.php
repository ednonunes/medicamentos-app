<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dose em Dia') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // Executa antes da página renderizar para evitar "piscadas" de cor
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-950 shadow border-b border-gray-100 dark:border-gray-800/60 transition-colors duration-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <footer class="w-full text-center py-6 text-xs text-slate-400 bg-white border-t border-slate-100 mt-auto">
                &copy; {{ date('Y') }} Dose em Dia. Todos os direitos reservados.<br>
                <span class="mt-1 block text-slate-500 font-medium">
                    Desenvolvido por <a href="mailto:ednonunes@gmail.com" class="hover:text-blue-600 transition">Edno Nunes Ferreira (ednonunes@gmail.com)</a>
                </span>
            </footer>
            
        </div>

        <x-confirm-modal />

    </body>
</html>
