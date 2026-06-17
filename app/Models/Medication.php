<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // 👈 Certifique-se de que o Carbon está importado aqui em cima

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dosage',
        'interval_hours',
        'days_of_week',
        'start_time',
        'observations',
        'take_on_empty_stomach',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'take_on_empty_stomach' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcula as próximas doses do medicamento para as próximas 24 horas.
     */
    public function getNextDoses(): array
    {
        $doses = [];
        
        // Tenta ler com segundos (padrão vindo do MySQL), se falhar lê sem segundos (padrão vindo de inputs/testes)
        try {
            $startTime = Carbon::createFromFormat('H:i:s', $this->start_time);
        } catch (\ArgumentsCountError|\Exception $e) {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
        }

        $interval = $this->interval_hours;

        // Alinha o primeiro horário para o início do dia atual
        $currentDose = Carbon::today()->setHour($startTime->hour)->setMinute($startTime->minute);

        // Retrocede o horário caso o início do remédio pertença ao ciclo de 24h anteriores
        while ($currentDose->copy()->subHours($interval)->isToday()) {
            $currentDose->subHours($interval);
        }

        // Gera todas as doses que se encaixam no dia de hoje
        while ($currentDose->isToday()) {
            $doses[] = $currentDose->format('H:i');
            $currentDose->addHours($interval);
        }

        return $doses;
    }

    /**
     * Registro dos medicamentos já consumidos
     */
    public function logs()
    {
        return $this->hasMany(MedicationLog::class);
    }

} 