<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-emerald-800 leading-tight">
            {{ __('Cadastrar Novo Medicamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                
                {{-- 🎯 Acoplado o evento onsubmit para disparar o loading --}}
                <form action="{{ route('medications.store') }}" method="POST" class="space-y-4" onsubmit="showLoadingForm(this)">
                    @csrf 

                    @include('medications.form')

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('medications.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition duration-150">
                            Cancelar
                        </a>
                        
                        {{-- 🎯 Botão adaptado com o spinner e os estados disabled --}}
                        <button type="submit" id="btn-salvar" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-900 rounded-md shadow-sm transition duration-150 disabled:opacity-75 disabled:cursor-not-allowed">
                            
                            <svg id="spinner-salvar" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <span id="texto-salvar">Salvar Remédio</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- 🛠️ Script que controla a ativação do loading ao submeter o formulário --}}
    <script>
        function showLoadingForm(form) {
            const btn = document.getElementById('btn-salvar');
            const spinner = document.getElementById('spinner-salvar');
            const texto = document.getElementById('texto-salvar');

            // Evita cliques repetidos travando o botão
            btn.disabled = true;
            
            // Exibe o ícone giratório do Tailwind
            spinner.classList.remove('hidden');
            
            // Muda o texto para dar o feedback visual
            texto.innerText = 'Salvando...';
        }
    </script>
</x-app-layout>