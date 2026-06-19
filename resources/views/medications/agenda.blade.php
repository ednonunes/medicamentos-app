<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-emerald-800 leading-tight">
                {{ __('Agenda de Doses') }}
            </h2>
            <a href="{{ $doctorLink }}" target="_blank" 
               class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                Ver Visualização Médica
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filtro de Data --}}
            <div class="bg-white p-4 mb-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-center gap-3">
                <label for="date" class="text-sm font-bold text-gray-600 uppercase">Visualizando doses do dia:</label>
                <form method="GET" action="{{ route('medications.agenda') }}">
                    <input type="date" id="date" name="date" value="{{ $dataSelecionada }}" onchange="this.form.submit()" 
                        class="border-gray-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-gray-700">
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    
                    @if(empty($agendaDoDia))
                        <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl">
                            <p class="text-gray-500">Nenhum medicamento agendado para esta data.</p>
                        </div>
                    @else
                        <div class="relative border-s border-gray-200 ml-4 md:ml-12 mt-4">
                            
                            @foreach($agendaDoDia as $item)
                                <div class="mb-10 ms-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-8 ring-white 
                                        {{ $item['ja_tomado'] ? 'bg-emerald-500' : ($item['ja_passou'] ? 'bg-rose-500' : 'bg-amber-500') }}">
                                        @if($item['ja_tomado'])
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                        @endif
                                    </span>

                                    <div class="p-4 border rounded-lg shadow-sm flex items-center justify-between gap-4">
                                        <div class="flex-1">
                                            <time class="text-lg font-bold">{{ $item['hora'] }}</time>
                                            <h3 class="text-base font-semibold text-gray-900">{{ $item['name'] }}</h3>
                                            <p class="text-sm text-gray-500">Dosagem: {{ $item['dosage'] }}</p>
                                        </div>

                                        <div class="flex-shrink-0">
                                            @if($item['ja_tomado'])
                                                {{-- Botão DESFAZER: Cinza suave com hover em vermelho --}}
                                                <form action="{{ route('medications.undo') }}" method="POST" onsubmit="showAgendaLoading(this)">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">
                                                    <input type="hidden" name="date" value="{{ $dataSelecionada }}">
                                                    <button type="submit" class="flex items-center gap-1 px-4 py-2 bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 border border-gray-200 hover:border-red-200 rounded-lg text-xs font-bold transition-all shadow-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        <span class="js-texto-btn">Desfazer</span>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Botão REGISTRAR: Verde vibrante para ação positiva --}}
                                                <form action="{{ route('medications.take') }}" method="POST" onsubmit="showAgendaLoading(this)">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">
                                                    <input type="hidden" name="date" value="{{ $dataSelecionada }}">
                                                    <button type="submit" class="flex items-center gap-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-md shadow-emerald-200 hover:shadow-emerald-300 js-texto-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        <span>Registrar</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function showAgendaLoading(form) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.innerText = 'Processando...'; }
        }
    </script>
</x-app-layout>