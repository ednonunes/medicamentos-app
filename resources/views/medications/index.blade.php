<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-emerald-800 dark:text-emerald-300 leading-tight">
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
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-emerald-800 dark:text-emerald-300 uppercase bg-emerald-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-semibold">Nome</th>
                                    <th scope="col" class="px-6 py-3 font-semibold">Dosagem</th>
                                    <th scope="col" class="px-6 py-3 font-semibold">Intervalo</th>
                                    <th scope="col" class="px-6 py-3 font-semibold">Hora Inicial</th>
                                    <th scope="col" class="px-6 py-3 font-semibold text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($medications as $medication)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">{{ $medication->name }}</td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $medication->dosage }}</td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">A cada {{ $medication->interval_hours }}h</td>
                                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($medication->start_time)->format('H:i') }}</td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium text-xs">Editar</button>
                                            <button class="text-red-600 dark:text-red-400 hover:underline font-medium text-xs">Excluir</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                            <p class="text-base font-medium">Nenhum medicamento cadastrado ainda.</p>
                                            <p class="text-xs mt-1">Clique em "+ Novo Medicamento" para adicionar o seu primeiro registro.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>