@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-md">
        <div class="flex">
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Ops! Corrija os erros abaixo:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

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
            class="mt-1 block w-full text-sm text-gray-500" >
        
        <small class="text-gray-500" id="info-fotos">
            Pode adicionar <span id="slots-restantes">3</span> fotos. <br>
            Tamanho máximo por foto: <strong>2MB</strong>.</small>
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
    // 1. Função de Atalhos
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

    // NOVA FUNÇÃO: Sincroniza os índices do HTML com o comportamento do array_values() do PHP
    function reindexarFotosEstaticas() {
        // Pega todos os blocos de fotos remanescentes que estão renderizados
        const containers = document.querySelectorAll('[id^="foto-item-"]');
        
        containers.forEach((div, novoIndice) => {
            // Atualiza o ID do próprio container container
            div.id = `foto-item-${novoIndice}`;
            
            // Localiza o botão dentro deste container e atualiza o seu evento de clique
            const botao = div.querySelector('button');
            if (botao) {
                // Extrai o ID do diário atual dinamicamente do atributo anterior
                const onClickAtual = botao.getAttribute('onclick');
                const matchStr = onClickAtual.match(/deletarFoto\('\d+',\s*'(\d+)'/);
                
                if (matchStr && matchStr[1]) {
                    const diaryId = matchStr[1];
                    // Atribui o novo índice corrigido ao botão
                    botao.setAttribute('onclick', `deletarFoto('${novoIndice}', '${diaryId}', 'foto-item-${novoIndice}')`);
                }
            }
        });
    }

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
                // Remove o elemento da tela
                document.getElementById(elementId).remove();
                fotosExistentes--; 
                atualizarSlots();  
                
                // CORREÇÃO DO BUG: Alinha os índices visuais com o banco após o array_values() do Laravel
                reindexarFotosEstaticas();
            } else {
                alert('Erro ao excluir foto.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Falha na conexão.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[type="file"][name="photos[]"]');
        const MAX_SIZE_MB = 2; // Ajuste para o mesmo valor do seu servidor

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const files = this.files;
                let errorFound = false;

                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > MAX_SIZE_MB * 1024 * 1024) {
                        // Chama o modal em vez do alert
                        exibirAvisoErro(
                            'Foto muito grande!', 
                            `A foto "${files[i].name}" excede o limite de ${MAX_SIZE_MB}MB e não será processada. Selecione uma imagem menor.`
                        );
                        this.value = ''; // Limpa o campo
                        errorFound = true;
                        break;
                    }
                }
            });
        }
    });

    // Nova função para disparar o seu componente x-confirm-modal
    function exibirAvisoErro(titulo, mensagem) {
        const modal = document.getElementById('global-confirm-modal');
        const titleEl = document.getElementById('modal-title');
        const msgEl = document.getElementById('modal-message');
        const confirmBtn = document.getElementById('modal-confirm-btn');
        const cancelBtn = document.getElementById('modal-cancel-btn');

        titleEl.innerText = titulo;
        msgEl.innerText = mensagem;
        
        // Esconde o botão de confirmar, pois é apenas um aviso
        confirmBtn.classList.add('hidden');
        cancelBtn.innerText = 'Entendido';

        modal.classList.remove('hidden');

        cancelBtn.onclick = () => {
            modal.classList.add('hidden');
            confirmBtn.classList.remove('hidden'); // Restaura para outros usos
        };
    }
</script>