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
        'daily_limit',
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
     * Calcula as doses do medicamento para uma data específica.
     * @param string|null $data A data no formato 'Y-m-d'
     */
    public function getNextDoses(?string $data = null): array
    {
        $doses = [];
        
        // 1. Define a data base: usa a fornecida ou hoje se for nulo
        $dataBase = $data ? Carbon::parse($data) : Carbon::today();

        // 2. Tenta ler o horário inicial
        try {
            $startTime = Carbon::createFromFormat('H:i:s', $this->start_time);
        } catch (\ArgumentsCountError|\Exception $e) {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
        }

        $interval = $this->interval_hours;

        // 3. Define o ponto de partida no dia correto (setando a hora do início do remédio)
        $currentDose = $dataBase->copy()->setHour($startTime->hour)->setMinute($startTime->minute);

        // 4. Gera as doses enquanto estiverem dentro do mesmo dia
        // Se o daily_limit existir, respeita ele; caso contrário, vai até o fim do dia
        $contador = 0;
        $limite = $this->daily_limit ?? 24; // Se não tiver limite, assume o dia todo

        while ($currentDose->isSameDay($dataBase) && $contador < $limite) {
            $doses[] = $currentDose->format('H:i');
            $currentDose->addHours($interval);
            $contador++;
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