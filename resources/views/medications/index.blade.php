<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-emerald-800 leading-tight">
                {{ __('Gerenciar Medicamentos') }}
            </h2>
            <a href="{{ route('medications.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition ease-in-out duration-150 shadow-sm">
                + Novo Medicamento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($medications->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-12 text-center text-gray-400">
                    <p class="text-base font-medium">Nenhum medicamento cadastrado ainda.</p>
                    <p class="text-xs mt-1">Clique em "+ Novo Medicamento" para adicionar o seu primeiro registro.</p>
                </div>
            @else

                {{-- Visualização Mobile --}}
                <div class="block md:hidden space-y-4">
                    @foreach($medications as $medication)
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-4">
                            <div class="flex justify-between items-start gap-2">
                                <div class="space-y-1">
                                    <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $medication->name }}</h3>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm text-gray-500">Dosagem: <span class="font-medium text-gray-700">{{ $medication->dosage }}</span></p>
                                        {{-- Badge Limite Diário --}}
                                        @if($medication->daily_limit)
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200">{{ $medication->daily_limit }}x/dia</span>
                                        @endif
                                    </div>
                                    @if(!empty($medication->observations))
                                        <p class="text-xs text-gray-400 font-normal">Obs: <span class="text-gray-500">{{ $medication->observations }}</span></p>
                                    @endif
                                </div>
                                
                                @if(isset($medication->take_on_empty_stomach) && $medication->take_on_empty_stomach)
                                    <span class="bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-md shrink-0">🍏 Jejum</span>
                                @else
                                    <span class="bg-slate-100 text-slate-600 text-xs font-medium px-2.5 py-1 rounded-md shrink-0">Alimentado</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 py-3 px-4 bg-slate-50 rounded-xl text-sm text-gray-600">
                                <div>
                                    <span class="block text-xxs text-gray-400 font-bold uppercase tracking-wider">Hora Inicial</span>
                                    <span class="font-bold text-gray-900 text-base">{{ \Carbon\Carbon::parse($medication->start_time)->format('H:i') }}</span>
                                </div>
                                <div>
                                    <span class="block text-xxs text-gray-400 font-bold uppercase tracking-wider">Intervalo</span>
                                    <span class="font-bold text-gray-900 text-base">A cada {{ $medication->interval_hours }}h</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <a href="{{ route('medications.edit', $medication->id) }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold py-3 px-4 rounded-xl transition text-sm text-center">Editar</a>
                                <form action="{{ route('medications.destroy', $medication->id) }}" method="POST" class="flex-1" onsubmit="openConfirmModal(event, 'Tem certeza que deseja excluir?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3 px-4 rounded-xl transition text-sm text-center">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Visualização Desktop --}}
                <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-emerald-800 uppercase bg-emerald-50/50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Medicamento</th>
                                        <th class="px-6 py-3">Dosagem / Obs</th>
                                        <th class="px-6 py-3">Rotina</th>
                                        <th class="px-6 py-3">Hora Inicial</th>
                                        <th class="px-6 py-3">Ingestão</th>
                                        <th class="px-6 py-3 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($medications as $medication)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-6 py-4 font-bold text-gray-900">{{ $medication->name }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-700">{{ $medication->dosage }}</div>
                                                @if($medication->daily_limit)
                                                    <div class="text-xs text-emerald-600 font-semibold">{{ $medication->daily_limit }} doses/dia</div>
                                                @endif
                                                @if(!empty($medication->observations))
                                                    <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs" title="{{ $medication->observations }}">Obs: {{ $medication->observations }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">A cada {{ $medication->interval_hours }}h</td>
                                            <td class="px-6 py-4 text-gray-700">{{ \Carbon\Carbon::parse($medication->start_time)->format('H:i') }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center text-xs font-semibold {{ $medication->take_on_empty_stomach ? 'text-amber-700 bg-amber-50' : 'text-slate-600 bg-slate-100' }} px-2 py-0.5 rounded">
                                                    {{ $medication->take_on_empty_stomach ? '🍏 Jejum' : 'Normal' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('medications.edit', $medication->id) }}" class="text-emerald-600 hover:text-emerald-800 mr-3">Editar</a>
                                                <form action="{{ route('medications.destroy', $medication->id) }}" method="POST" class="inline" onsubmit="openConfirmModal(event, 'Tem certeza?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 px-2 md:px-0">
                    {{ $medications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>