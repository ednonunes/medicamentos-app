<?php

use App\Models\Medication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class LembreteMedicamentoNotification extends Notification
{
    use Queueable;

    protected $medication;
    protected $horario;

    public function __construct(Medication $medication, $horario)
    {
        $this->medication = $medication;
        $this->horario = $horario;
    }

    // Definimos que essa notificação usa um canal customizado
    public function via($notifiable): array
    {
        return ['whatsapp'];
    }

    // O método que faz a mágica e envia a mensagem para a API externa
    public function toWhatsapp($notifiable)
    {
        // Estruturando um texto amigável e profissional
        $texto = "⚠️ *Lembrete Dose em Dia* ⚠️\n\n";
        $texto .= "Olá, {$notifiable->name}! Está na hora de tomar seu medicamento:\n\n";
        $texto .= "💊 *Medicamento:* {$this->medication->name}\n";
        $texto .= "🕒 *Horário:* {$this->horario}\n";

        // Adiciona o alerta de jejum se a flag estiver ativa
        if ($this->medication->take_on_empty_stomach) {
            $texto .= "🍏 *Aviso:* Este medicamento deve ser tomado em *JEJUM*.\n";
        }

        // Adiciona as observações se houver
        if ($this->medication->observations) {
            $texto .= "📝 *Notas médicas:* _{$this->medication->observations}_\n";
        }

        $texto .= "\nNão se esqueça de entrar no sistema para marcar como tomado! 👍";

        // Disparo HTTP para a API de sua escolha (Exemplo genérico usando Z-API)
        // Substitua a URL e os Tokens pelas credenciais fornecidas pela API escolhida
        return Http::withHeaders([
            'Client-Token' => config('services.whatsapp.token')
        ])->post(config('services.whatsapp.url'), [
            'phone' => $notifiable->phone, // O número do celular do usuário salvo no banco
            'message' => $texto
        ]);
    }
}