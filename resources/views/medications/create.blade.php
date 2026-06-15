<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-emerald-800 dark:text-emerald-300 leading-tight">
            {{ __('Cadastrar Novo Medicamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                
                <form action="{{ route('medications.store') }}" method="POST" class="space-y-4">
                    @csrf 

                    @include('medications.form')

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('medications.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">Cancelar</a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-900 rounded-md shadow-sm transition duration-150">Salvar Remédio</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>