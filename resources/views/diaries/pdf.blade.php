<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório - Diário de Saúde</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-align: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #047857;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h2 {
            color: #065f46;
            margin: 0 0 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }
        .entry-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            background-color: #f9fafb;
            page-break-inside: avoid;
        }
        .entry-header {
            font-weight: bold;
            color: #047857;
            font-size: 11px;
            margin-bottom: 6px;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }
        .entry-content {
            color: #1f2937;
            white-space: pre-line;
            margin-bottom: 10px;
        }
        .photos-container {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #e5e7eb;
        }
        .photo-thumb {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-block;
        }
        .footer {
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Relatório do Diário de Saúde</h2>
        <p>
            Paciente: <strong>{{ auth()->user()->name }}</strong> | 
            Período: 
            @if(!empty($dataInicio) && !empty($dataFim))
                {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
            @elseif(!empty($dataInicio))
                {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}
            @else
                Geral
            @endif
        </p>
    </div>

    @if($diaries->isEmpty())
        <p style="text-align: center; color: #9ca3af; margin-top: 40px;">Nenhum registro encontrado para este período.</p>
    @else
        @foreach($diaries as $diary)
            <div class="entry-box">
                <!-- Data e Hora do Registro -->
                <div class="entry-header">
                    {{ $diary->entry_datetime->format('d/m/Y H:i') }}
                </div>

                <!-- Texto/Conteúdo -->
                <div class="entry-content">
                    {{ $diary->content }}
                </div>

                <!-- Fotos Abaixo do Texto (Lado a Lado) -->
                @if($diary->photos && is_array($diary->photos) && count($diary->photos) > 0)
                    <div class="photos-container">
                        @foreach($diary->photos as $photo)
                            @php
                                $path = storage_path('app/public/' . $photo);
                            @endphp
                            @if(file_exists($path))
                                <img src="{{ $path }}" class="photo-thumb">
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <div class="footer">
        Gerado em {{ date('d/m/Y H:i') }} por Diário de Saúde &bull; Página 1
    </div>

</body>
</html>