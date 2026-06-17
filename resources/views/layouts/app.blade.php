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

            <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] z-50">
                <div class="flex justify-around items-center h-16">
                    
                    {{-- Botão 1: Início / Dashboard --}}
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-center {{ request()->routeIs('dashboard') ? 'text-emerald-600 font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-xxs mt-1 tracking-wide">Início</span>
                    </a>

                    {{-- Botão 2: Gerenciar Remédios (Index) --}}
                    <a href="{{ route('medications.index') }}" class="flex flex-col items-center justify-center w-full h-full text-center {{ request()->routeIs('medications.index') ? 'text-emerald-600 font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 002-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="text-xxs mt-1 tracking-wide">Remédios</span>
                    </a>

                    {{-- Botão 3: Cadastrar Novo (+ Rápido) --}}
                    <a href="{{ route('medications.create') }}" class="flex flex-col items-center justify-center w-full h-full text-center {{ request()->routeIs('medications.create') ? 'text-emerald-600 font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                        <div class="bg-emerald-50 text-emerald-600 p-2 rounded-xl -mt-4 shadow-sm border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                            </svg>
                        </div>
                        <span class="text-xxs mt-1 tracking-wide">Novo</span>
                    </a>

                    {{-- Botão 4: Perfil / Minha Conta --}}
                    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center w-full h-full text-center {{ request()->routeIs('profile.edit') ? 'text-emerald-600 font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-xxs mt-1 tracking-wide">Perfil</span>
                    </a>

                </div>
            </div>

            <div class="h-20 md:hidden"></div>

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
