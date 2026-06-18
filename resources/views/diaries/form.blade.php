<div class="space-y-4">
    {{-- Atalhos Rápidos --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach(['Pressão Arterial', 'Urina', 'Medicamento Tomado', 'Sintoma'] as $atalho)
            <button type="button" 
                    onclick="adicionarAtalho('{{ $atalho }}')"
                    class="text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full hover:bg-emerald-200 transition">
                + {{ $atalho }}
            </button>
        @endforeach
    </div>

    {{-- Data e Hora --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Data e Hora</label>
        <input type="datetime-local" name="entry_datetime" 
               value="{{ old('entry_datetime', isset($diary) ? $diary->entry_datetime->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}" 
               required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    {{-- Conteúdo --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">O que deseja registrar?</label>
        <textarea id="content-textarea" name="content" rows="4" required 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                  placeholder="Selecione um atalho acima ou digite aqui...">{{ old('content', $diary->content ?? '') }}</textarea>
    </div>

    {{-- Input de Fotos --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Fotos (máx 3)</label>
        <input type="file" name="photos[]" multiple accept="image/*" id="photo-input"
            class="mt-1 block w-full text-sm text-gray-500">
        <small class="text-gray-500" id="info-fotos">Pode adicionar <span id="slots-restantes">3</span> fotos.</small>
    </div>

    {{-- Bloco de Fotos na Edição --}}
    @if(isset($diary) && $diary->photos && count($diary->photos) > 0)
        <div class="mt-6" id="container-fotos">
            <label class="block text-sm font-medium text-gray-700 mb-3">Fotos cadastradas:</label>
            <div class="grid grid-cols-2 gap-4">
                @foreach($diary->photos as $index => $photo)
                    <div class="flex flex-col gap-2" id="foto-item-{{ $index }}">
                        <div class="w-full aspect-square overflow-hidden rounded-lg border border-gray-200">
                            <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover">
                        </div>
                        <button type="button" 
                                onclick="deletarFoto('{{ $index }}', '{{ $diary->id }}', 'foto-item-{{ $index }}')"
                                class="w-full py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-bold hover:bg-red-100 transition active:scale-95">
                            Excluir Foto
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>

    // 1. Função de Atalhos (A que estava faltando)
    function adicionarAtalho(texto) {
        const textarea = document.getElementById('content-textarea');
        const valorAtual = textarea.value;
        textarea.value = valorAtual + (valorAtual ? "\n" : "") + texto + ": ";
        textarea.focus();
    }
    
    // Inicializa o contador baseado no que já existe no banco
    let fotosExistentes = {{ isset($diary) && $diary->photos ? count($diary->photos) : 0 }};
    
    function atualizarSlots() {
        const restantes = 3 - fotosExistentes;
        document.getElementById('slots-restantes').innerText = restantes;
        return restantes;
    }

    // Chama na carga da página
    atualizarSlots();

    document.getElementById('photo-input').addEventListener('change', function() {
        const limite = atualizarSlots();
        if (this.files.length > limite) {
            alert('Você pode selecionar no máximo ' + limite + ' foto(s) adicional(is).');
            this.value = ''; 
        }
    });

    async function deletarFoto(index, diaryId, elementId) {
        if (!confirm('Deseja realmente excluir esta foto?')) return;

        try {
            const response = await fetch(`/diaries/${diaryId}/photos/${index}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                document.getElementById(elementId).remove();
                fotosExistentes--; // Decrementa o contador
                atualizarSlots();  // Atualiza o texto de ajuda
            } else {
                alert('Erro ao excluir foto.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Falha na conexão.');
        }
    }
    
    // ... (função adicionarAtalho permanece igual) ...
</script>