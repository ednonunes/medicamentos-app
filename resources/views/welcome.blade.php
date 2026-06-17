<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dose em Dia - Lembretes de Medicamentos</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            
            <center><x-application-logo class="w-48 h-48 object-contain" /></center>

            <p class="text-slate-500 mb-10 text-base">
                Seu assistente inteligente de saúde. Nunca mais se esqueça do horário dos seus medicamentos.
            </p>

            <div class="space-y-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-200 shadow-lg shadow-blue-100 text-lg">
                            Acessar Meu Painel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-200 shadow-lg shadow-blue-100 text-lg">
                            Entrar na Conta
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-center bg-white hover:bg-slate-50 text-slate-700 font-semibold py-4 px-6 rounded-xl border border-slate-200 transition duration-200 text-lg">
                                Criar Nova Conta
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-center gap-2 text-xs text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-emerald-500">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                </svg>
                Conexão segura com criptografia ponta a ponta
            </div>

        </div>
    </main>

    <footer class="w-full text-center py-6 text-xs text-slate-400">
        &copy; {{ date('Y') }} Dose em Dia. Todos os direitos reservados.<br>
        <span class="mt-1 block text-slate-300 font-medium">
            Desenvolvido por <a href="mailto:ednonunes@gmail.com" class="hover:text-blue-500 transition">Edno Nunes Ferreira (ednonunes@gmail.com)</a>
        </span>
    </footer>

</body>
</html>