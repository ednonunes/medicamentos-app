<style>
    body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
    h1 { color: #065f46; border-bottom: 2px solid #065f46; padding-bottom: 10px; }
    
    .registro { 
        margin-bottom: 30px; 
        padding: 15px; 
        border: 1px solid #e5e7eb; 
        border-radius: 8px; 
        page-break-inside: avoid;
    }
    
    .header-registro { 
        font-weight: bold; 
        color: #059669; 
        margin-bottom: 10px; 
        display: block;
    }
    
    .conteudo {
        white-space: pre-wrap !important;
        margin: 0 !important;
        padding: 0 !important;
        text-indent: 0 !important;
        line-height: 1.5 !important;
        word-wrap: break-word !important;
        letter-spacing: normal !important;
    }
    
    .foto-container { margin-top: 10px; }
    
    /* Classe de máscara para evitar distorção da imagem */
    .foto-mask {
        width: 150px;
        height: 150px;
        display: inline-block;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-right: 10px;
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;
    }
</style>

<h1>Relatório de Saúde</h1>

@foreach($diaries as $diary)
    <div class="registro">
        <span class="header-registro">
            Data: {{ $diary->entry_datetime->format('d/m/Y') }} às {{ $diary->entry_datetime->format('H:i') }}
        </span>
        
        <div class="conteudo">{{ trim($diary->content) }}</div>
        
        @if($diary->photos && count($diary->photos) > 0)
            <div class="foto-container">
                @foreach($diary->photos as $photo)
                    {{-- Usamos div com background-image para o efeito "center-crop" --}}
                    <div class="foto-mask" style="background-image: url('{{ public_path('storage/' . $photo) }}');"></div>
                @endforeach
            </div>
        @endif
    </div>
@endforeach