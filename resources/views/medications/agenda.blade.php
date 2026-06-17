<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Próximas Doses do Dia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">
                            Acompanhe a ordem cronológica dos seus medicamentos agendados para hoje, <span class="font-bold text-emerald-600">{{ now()->format('d/m/Y') }}</span>.
                        </p>
                    </div>

                    @if(empty($agendaDoDia))
                        {{-- 1️⃣ CENÁRIO: Nenhum remédio configurado para passar no dia de hoje --}}
                        <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl">
                            <p class="text-gray-500">Nenhum medicamento agendado para hoje.</p>
                        </div>
                    @else

                        {{-- 🛠️ Conta se existe alguma dose na lista que ainda NÃO foi tomada --}}
                        @php
                            $dosesPendentes = collect($agendaDoDia)->where('ja_tomado', false)->count();
                        @endphp

                        @if($dosesPendentes === 0)
                            {{-- 2️⃣ CENÁRIO: Existem remédios hoje, mas TODOS já foram tomados --}}
                            <div class="text-center py-12 bg-emerald-50/50 border border-emerald-100 rounded-xl mb-6">
                                <span class="text-3xl block mb-2">🎉</span>
                                <h3 class="text-lg font-bold text-emerald-800">Tudo pronto por hoje!</h3>
                                <p class="text-sm text-emerald-600 mt-1">Você já tomou todos os medicamentos agendados para hoje.</p>
                            </div>
                        @endif

                        {{-- Mantém a listagem abaixo da mensagem de sucesso para permitir que o usuário veja o histórico ou use o "Desfazer" --}}
                        <div class="relative border-s border-gray-200 ml-4 md:ml-12 mt-4">
                            
                            @foreach($agendaDoDia as $item)
                                <div class="mb-10 ms-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-8 ring-white 
                                        {{ $item['ja_tomado'] ? 'bg-emerald-500' : ($item['ja_passou'] ? 'bg-rose-500' : 'bg-amber-500 animate-pulse') }}">
                                        
                                        @if($item['ja_tomado'])
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                            </svg>
                                        @elseif($item['ja_passou'])
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                            </svg>
                                        @else
                                            <div class="w-2 h-2 rounded-full bg-white"></div>
                                        @endif
                                    </span>

                                    <div class="p-4 border rounded-lg shadow-sm flex items-center justify-between gap-4 transition-colors duration-150
                                        {{ $item['ja_tomado'] 
                                            ? 'border-emerald-200 bg-emerald-50/25' 
                                            : ($item['ja_passou'] 
                                                ? 'border-rose-200 bg-rose-50/30' 
                                                : 'border-amber-200 bg-amber-50/25') }}">
                                        
                                        <div class="flex-1 min-w-0">
                                            <time class="mb-1 text-lg font-bold leading-none block 
                                                {{ $item['ja_tomado'] ? 'text-emerald-600' : ($item['ja_passou'] ? 'text-rose-600' : 'text-amber-600') }}">
                                                {{ $item['hora'] }}
                                            </time>
                                            <h3 class="text-base font-semibold text-gray-900 truncate">
                                                {{ $item['name'] }}
                                            </h3>
                                            <p class="text-sm font-normal text-gray-500">
                                                Dosagem: <span class="font-medium text-gray-700">{{ $item['dosage'] }}</span>
                                            </p>

                                            @if(!empty($item['days_of_week']))
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <span class="font-medium">Dias:</span> {{ implode(', ', $item['days_of_week']) }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex-shrink-0">
                                            @if($item['ja_tomado'])
                                                
                                                <form action="{{ route('medications.undo') }}" method="POST" class="inline js-form-agenda" onsubmit="showAgendaLoading(this)">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">

                                                    <button type="submit" class="group inline-flex items-center gap-1.5 text-xs bg-emerald-100 hover:bg-rose-50 text-emerald-800 hover:text-rose-600 px-3 py-1.5 rounded-full font-bold border border-emerald-200 hover:border-rose-200 shadow-sm transition-all ease-in-out duration-150 whitespace-nowrap disabled:opacity-75 disabled:cursor-not-allowed">
                                                        
                                                        <svg class="js-spinner hidden animate-spin h-3.5 w-3.5 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>

                                                        <svg class="js-icon-check w-3.5 h-3.5 text-emerald-600 group-hover:hidden" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                                        </svg>
                                                        
                                                        <svg class="js-icon-undo w-3.5 h-3.5 text-rose-500 hidden group-hover:block" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                                                        </svg>

                                                        <span class="js-texto-btn">Tomado</span>
                                                    </button>
                                                </form>

                                            @else
                                                
                                                <form action="{{ route('medications.take') }}" method="POST" class="inline js-form-agenda" onsubmit="showAgendaLoading(this)">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">
                                                    
                                                    <button type="submit" class="inline-flex items-center justify-center gap-2 text-xs bg-white px-3 py-1.5 rounded-md font-semibold border shadow-sm transition ease-in-out duration-150 whitespace-nowrap disabled:opacity-75 disabled:cursor-not-allowed
                                                        {{ $item['ja_passou'] 
                                                            ? 'border-rose-200 text-rose-600 hover:bg-rose-50' 
                                                            : 'border-amber-200 text-amber-600 hover:bg-amber-50' }}">
                                                        
                                                        <svg class="js-spinner hidden animate-spin h-3.5 w-3.5 {{ $item['ja_passou'] ? 'text-rose-600' : 'text-amber-600' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>

                                                        <span class="js-texto-btn">{{ $item['ja_passou'] ? 'Registrar Atrasado' : 'Marcar como Tomado' }}</span>
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
            const spinner = form.querySelector('.js-spinner');
            const texto = form.querySelector('.js-texto-btn');
            
            const iconCheck = form.querySelector('.js-icon-check');
            const iconUndo = form.querySelector('.js-icon-undo');

            if (btn && spinner && texto) {
                btn.disabled = true;
                
                if (iconCheck) iconCheck.classList.add('hidden');
                if (iconUndo) iconUndo.classList.add('hidden');
                
                spinner.classList.remove('hidden');
                texto.innerText = 'Processando...';
            }
        }
    </script>
</x-app-layout>