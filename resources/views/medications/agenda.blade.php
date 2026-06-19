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

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filtro de Data --}}
            <div class="bg-white p-3 mb-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-center gap-3">
                <label for="date" class="text-xs font-bold text-gray-600 uppercase">Visualizando doses do dia:</label>
                <form method="GET" action="{{ route('medications.agenda') }}">
                    <input type="date" id="date" name="date" value="{{ $dataSelecionada }}" onchange="this.form.submit()" 
                        class="border-gray-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-1 text-gray-700">
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4 text-gray-900">
                    
                    @if(empty($agendaDoDia))
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                            <p class="text-gray-500 text-sm">Nenhum medicamento agendado para esta data.</p>
                        </div>
                    @else
                        <div class="relative border-s-2 border-gray-100 ml-4 mt-2">
                            
                            @foreach($agendaDoDia as $item)
                                <div class="mb-4 ms-5">
                                    {{-- Indicador de status --}}
                                    <span class="absolute flex items-center justify-center w-5 h-5 rounded-full -left-[11px] ring-4 ring-white 
                                        {{ $item['ja_tomado'] ? 'bg-emerald-500' : ($item['ja_passou'] ? 'bg-rose-500' : 'bg-amber-500') }}">
                                        @if($item['ja_tomado'])
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                        @endif
                                    </span>

                                    <div class="p-3 border border-gray-100 rounded-lg shadow-sm flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-baseline gap-2">
                                                <time class="text-sm font-bold text-gray-900">{{ $item['hora'] }}</time>
                                                <h3 class="text-sm font-bold text-gray-800 truncate">{{ $item['name'] }}</h3>
                                            </div>
                                            <p class="text-[10px] text-gray-500">{{ $item['dosage'] }}</p>
                                            
                                            {{-- Observação integrada --}}
                                            @if(!empty($item['observations']))
                                                <p class="mt-1 text-[10px] text-gray-600 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 italic">
                                                    {{ $item['observations'] }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex-shrink-0">
                                            @if($item['ja_tomado'])
                                                <form action="{{ route('medications.undo') }}" method="POST" onsubmit="showAgendaLoading(this)">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">
                                                    <input type="hidden" name="date" value="{{ $dataSelecionada }}">
                                                    <button type="submit" class="px-3 py-1.5 bg-gray-50 hover:bg-red-50 text-gray-500 hover:text-red-600 border border-gray-200 rounded-md text-[10px] font-bold transition-all">
                                                        Desfazer
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('medications.take') }}" method="POST" onsubmit="showAgendaLoading(this)">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">
                                                    <input type="hidden" name="date" value="{{ $dataSelecionada }}">
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-[10px] font-bold transition-all shadow-sm">
                                                        Registrar
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
            if (btn) { btn.disabled = true; btn.innerText = '...'; }
        }
    </script>
</x-app-layout>