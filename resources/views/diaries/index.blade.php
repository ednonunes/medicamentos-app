<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-emerald-800 leading-tight">
                {{ __('Meu Diário de Saúde') }}
            </h2>
            <a href="{{ route('diaries.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 text-white text-sm font-medium rounded-lg transition ease-in-out duration-150 shadow-sm">
                + Novo Registro
            </a>
        </div>
    </x-slot>
    

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- filtros --}}
            <div class="mb-6">
                <form action="{{ route('diaries.index') }}" method="GET" class="flex items-center gap-2 overflow-x-auto pb-2">
                    {{-- Título --}}
                    <span class="text-sm font-bold text-gray-700 mr-2 flex-shrink-0">Filtros:</span>
                    
                    {{-- Botões de Filtro --}}
                    @foreach(['Pressão Arterial', 'Urina', 'Medicamento', 'Sintoma'] as $filtro)
                        @php
                            $selecionado = in_array($filtro, (array)request()->query('categorias', []));
                        @endphp
                        
                        <label class="cursor-pointer whitespace-nowrap">
                            <input type="checkbox" name="categorias[]" value="{{ $filtro }}" class="hidden peer" 
                                onchange="this.form.submit()" {{ $selecionado ? 'checked' : '' }}>
                            
                            <span class="px-4 py-2 text-sm rounded-full transition-all border 
                                {{ $selecionado 
                                    ? 'bg-emerald-600 text-white border-emerald-600' 
                                    : 'bg-white text-emerald-800 border-emerald-200 hover:bg-emerald-50' }}">
                                {{ $filtro }}
                            </span>
                        </label>
                    @endforeach

                    {{-- Contêiner alinhado à direita --}}
                    <div class="ml-auto flex items-center gap-2">
                        
                        {{-- Botão Limpar --}}
                        @if(request()->has('categorias'))
                            <a href="{{ route('diaries.index') }}" 
                            class="px-4 py-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-full hover:bg-blue-100 transition whitespace-nowrap">
                            Limpar filtros
                            </a>
                        @endif

                        {{-- Botão Exportar --}}
                        <a href="{{ route('diaries.export', request()->query()) }}" 
                        target="_blank" class="px-4 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full hover:bg-emerald-100 transition whitespace-nowrap">
                        Exportar PDF
                        </a>
                    </div>
                </form>
            </div>

            {{-- alertas --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($diaries->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-12 text-center text-gray-400">
                    <p class="text-base font-medium">Nenhum registro no diário ainda.</p>
                    <p class="text-xs mt-1">Clique em "+ Novo Registro" para começar a documentar sua saúde.</p>
                </div>
            @else
                {{-- Visualização Mobile --}}
                <div class="block md:hidden space-y-4">
                    @foreach($diaries as $diary)
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">
                                    {{ $diary->entry_datetime->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <p class="text-gray-700 text-sm whitespace-pre-line">{{ $diary->content }}</p>
                            
                            @if($diary->photos && count($diary->photos) > 0)
                                <div class="flex gap-2 pt-2">
                                    @foreach($diary->photos as $photo)
                                        <img src="{{ asset('storage/' . $photo) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-100">
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center gap-2 pt-2">
                                {{-- Editar Mobile --}}
                                <a href="{{ route('diaries.edit', $diary->id) }}" 
                                   class="flex-1 inline-flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold py-2 px-4 rounded-xl transition text-sm gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                    Editar
                                </a>
                                {{-- Excluir Mobile --}}
                                <form action="{{ route('diaries.destroy', $diary->id) }}" method="POST" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este registro?')" 
                                      class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full inline-flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2 px-4 rounded-xl transition text-sm gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                        Excluir
                                    </button>
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
                                        <th class="px-6 py-3">Data e Hora</th>
                                        <th class="px-6 py-3">Conteúdo</th>
                                        <th class="px-6 py-3">Fotos</th>
                                        <th class="px-6 py-3 text-center w-28">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($diaries as $diary)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $diary->entry_datetime->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-700 max-w-lg truncate">
                                                {{ $diary->content }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($diary->photos)
                                                    {{ count($diary->photos) }} foto(s)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center justify-center gap-3">
                                                    {{-- Ícone Editar Desktop --}}
                                                    <a href="{{ route('diaries.edit', $diary->id) }}" 
                                                       class="text-emerald-600 hover:text-emerald-800 transition p-1 hover:bg-emerald-50 rounded"
                                                       title="Editar Registro">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                        </svg>
                                                    </a>

                                                    {{-- Ícone Excluir Desktop --}}
                                                    <form action="{{ route('diaries.destroy', $diary->id) }}" method="POST" 
                                                          onsubmit="openConfirmModal(event, 'Tem certeza que deseja excluir?');"
                                                          class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-800 transition p-1 hover:bg-red-50 rounded"
                                                                title="Excluir Registro">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>