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
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center space-x-3">
                                                <button title="Editar Medicamento" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition duration-150 focus:outline-none">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path>
                                                    </svg>
                                                </button>

                                                <form action="{{ route('medications.destroy', $medication->id) }}" method="POST" class="inline" onsubmit="openConfirmModal(event, 'Tem certeza que deseja excluir este medicamento? Esta ação não poderá ser desfeita.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    
                                                    <button type="submit" title="Excluir Medicamento" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition duration-150 focus:outline-none align-middle">
                                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
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