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

            {{-- filtros --}}
            
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

                            <div class="flex items-center gap-3 pt-2">
                                <a href="{{ route('diaries.edit', $diary->id) }}" class="flex-1 inline-flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold py-2 px-4 rounded-xl transition text-sm">Editar</a>
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
                                        <th class="px-6 py-3 text-right">Ações</th>
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
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('diaries.edit', $diary->id) }}" class="text-emerald-600 hover:text-emerald-800">Editar</a>
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