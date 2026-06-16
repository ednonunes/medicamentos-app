<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-emerald-800 dark:text-emerald-300 leading-tight">
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
                                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-8 ring-white dark:ring-gray-800 {{ $item['ja_passou'] ? 'bg-gray-200 dark:bg-gray-700' : 'bg-emerald-500 animate-pulse' }}">
                                        @if($item['ja_passou'])
                                            <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <div class="w-2 h-2 rounded-full bg-white"></div>
                                        @endif
                                    </span>

                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800/80 rounded-lg shadow-sm flex items-center justify-between {{ $item['ja_passou'] ? 'opacity-50' : '' }}">
                                        <div>
                                            <time class="mb-1 text-lg font-bold leading-none text-emerald-600 dark:text-emerald-400 block">
                                                {{ $item['hora'] }}
                                            </time>
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $item['name'] }}
                                            </h3>
                                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                Dosagem: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $item['dosage'] }}</span>
                                            </p>
                                        </div>

                                        <div>
                                            @if($item['ja_passou'])
                                                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2.5 py-1 rounded-md font-medium">Horário Passado</span>
                                            @else
                                                <span class="text-xs bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 px-2.5 py-1 rounded-md font-semibold border border-emerald-200 dark:border-emerald-900/50">Pendente</span>
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