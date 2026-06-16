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
    ];

    protected $casts = [
        'days_of_week' => 'array',
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
        $interval = $this->interval_hours;
        
        // Cria o Carbon com a hora inicial definida para o dia de hoje
        $firstDose = Carbon::createFromFormat('H:i:s', $this->start_time);
        
        // Adiciona a primeira dose do dia
        $doses[] = $firstDose->format('H:i');

        $currentDose = $firstDose;
        
        // Loop para preencher o restante do dia (máximo de 24 horas)
        for ($i = 1; $i < (24 / $interval); $i++) {
            $currentDose = $currentDose->copy()->addHours($interval);
            $doses[] = $currentDose->format('H:i');
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