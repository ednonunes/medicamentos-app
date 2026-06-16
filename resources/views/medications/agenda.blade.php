<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Próximas Doses do Dia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700/50">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Acompanhe a ordem cronológica dos seus medicamentos agendados para hoje, <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ now()->format('d/m/Y') }}</span>.
                        </p>
                    </div>

                    @if(empty($agendaDoDia))
                        <div class="text-center py-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                            <p class="text-gray-500 dark:text-gray-400">Nenhum medicamento agendado para hoje.</p>
                        </div>
                    @else
                        <div class="relative border-s border-gray-200 dark:border-gray-700 ml-4 md:ml-12 mt-4">
                            
                            @foreach($agendaDoDia as $item)
                                <div class="mb-10 ms-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-8 ring-white dark:ring-gray-800 
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
                                            ? 'border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/25 dark:bg-emerald-950/10' 
                                            : ($item['ja_passou'] 
                                                ? 'border-rose-200 dark:border-rose-900/30 bg-rose-50/30 dark:bg-rose-950/10' 
                                                : 'border-amber-200 dark:border-amber-900/30 bg-amber-50/25 dark:bg-amber-950/5') }}">
                                        
                                        <div class="flex-1 min-w-0">
                                            <time class="mb-1 text-lg font-bold leading-none block 
                                                {{ $item['ja_tomado'] ? 'text-emerald-600 dark:text-emerald-400' : ($item['ja_passou'] ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                                {{ $item['hora'] }}
                                            </time>
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $item['name'] }}
                                            </h3>
                                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                Dosagem: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $item['dosage'] }}</span>
                                            </p>

                                            @if(!empty($item['days_of_week']))
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    <span class="font-medium">Dias:</span> {{ implode(', ', $item['days_of_week']) }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex-shrink-0">
                                            @if($item['ja_tomado'])
                                                <span class="inline-flex items-center gap-1.5 text-xs bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 px-3 py-1.5 rounded-full font-bold border border-emerald-200 dark:border-emerald-800 shadow-sm">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                                    </svg>
                                                    Tomado
                                                </span>
                                            @else
                                                <form action="{{ route('medications.take') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="medication_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="scheduled_time" value="{{ $item['hora'] }}">
                                                    
                                                    <button type="submit" class="text-xs bg-white dark:bg-gray-800 px-3 py-1.5 rounded-md font-semibold border shadow-sm transition ease-in-out duration-150 whitespace-nowrap
                                                        {{ $item['ja_passou'] 
                                                            ? 'border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30' 
                                                            : 'border-amber-200 dark:border-amber-700 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30' }}">
                                                        {{ $item['ja_passou'] ? 'Registrar Atrasado' : 'Marcar como Tomado' }}
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
</x-app-layout>