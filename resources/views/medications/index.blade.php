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
            <div class="bg-white dark:bg-gray-800 p-6 shadow-sm rounded-xl">
                <p class="text-gray-600 dark:text-gray-400">Aqui você poderá ver, editar e excluir seus medicamentos.</p>
                
                </div>
        </div>
    </div>
</x-app-layout>