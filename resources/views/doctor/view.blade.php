<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário Médico - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 pb-10 min-h-screen flex flex-col">

    <div class="bg-white border-b border-gray-200 p-6 mb-8">
        <div class="max-w-4xl mx-auto flex justify-between items-start">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Relatório de Saúde: <span class="text-emerald-700">{{ $user->name }}</span></h1>
                <p class="text-sm text-gray-500 mt-1 font-medium italic">
                    Data de referência: {{ \Carbon\Carbon::parse($data)->translatedFormat('d \d\e F \d\e Y') }}
                </p>
            </div>
            <a href="{{ url('/') }}" class="transition-opacity hover:opacity-80">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain">
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 space-y-8 flex-grow">

        <section>
            <div class="flex items-center gap-2 mb-4 text-gray-800">
                <span class="p-1.5 bg-emerald-100 text-emerald-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                <h2 class="font-bold text-sm uppercase tracking-wider">Agenda de Medicamentos</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @forelse($agendaEspelho as $item)
                    <div class="bg-white p-3 rounded-lg border {{ $item['tomado'] ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200' }} shadow-sm flex flex-col justify-between">
                        <div>
                            <p class="text-xs font-bold {{ $item['tomado'] ? 'text-emerald-900' : 'text-gray-800' }} truncate">
                                {{ $item['name'] }}
                            </p>
                            <p class="text-[10px] text-gray-500 font-medium">{{ $item['dosage'] }}</p>
                        </div>
                        
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[10px] font-bold {{ $item['tomado'] ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $item['scheduled_time'] }} {{ $item['tomado'] ? '✓' : '' }}
                            </span>
                            
                            @if($item['take_on_empty_stomach'])
                                <span class="text-[9px] bg-amber-50 text-amber-700 px-1 rounded border border-amber-200">Jejum</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 italic text-sm">Nenhum medicamento agendado para esta data.</p>
                @endforelse
            </div>
        </section>

        <section>
            <div class="flex items-center gap-2 mb-4 text-gray-800">
                <span class="p-1.5 bg-blue-100 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </span>
                <h2 class="font-bold text-sm uppercase tracking-wider">Diário do Paciente</h2>
            </div>

            <div class="space-y-4">
                @forelse($diaries as $diary)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-bold text-emerald-600">{{ $diary->entry_datetime->format('H:i') }}</span>
                            <span class="px-2 py-0.5 bg-gray-50 text-gray-500 text-[10px] font-bold uppercase tracking-wider rounded border border-gray-100">
                                {{ $diary->category ?? 'Registro' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 leading-relaxed">{{ $diary->content }}</div>
                        
                        @if($diary->photos && count($diary->photos) > 0)
                            <div class="grid grid-cols-3 gap-2 mt-4">
                                @foreach($diary->photos as $photo)
                                    <div class="aspect-square rounded-md overflow-hidden bg-gray-100">
                                        <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 bg-white rounded-xl border border-dashed border-gray-200">
                        <p class="text-xs text-gray-400">Nenhum registro de diário encontrado.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <footer class="mt-12 text-center text-[10px] text-gray-400 border-t pt-4">
        <a href="{{ url('/') }}" class="hover:text-emerald-600 underline">
            Desenvolvido por Edno Nunes Ferreira (ednonunes@gmail.com)
        </a>
    </footer>

</body>
</html>