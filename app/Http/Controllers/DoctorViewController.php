<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MedicationLog;
use Carbon\Carbon;

class DoctorViewController extends Controller
{
    public function show(Request $request, User $user)
    {
        if (! $request->hasValidSignature()) {
           // abort(403, 'Link expirado ou inválido.');
        }

        $data = $request->query('date', now()->format('Y-m-d'));
        
        // 1. Busca diários
        $diaries = $user->diaries()->whereDate('entry_datetime', $data)->orderBy('entry_datetime', 'asc')->get();
        
        // 2. Busca logs tomados para cruzar com a agenda
        $logsTomados = \App\Models\MedicationLog::whereIn('medication_id', $user->medications()->pluck('id'))
            ->whereDate('taken_at', $data)
            ->get();

        // 3. Monta a grade espelho da agenda
        $agendaEspelho = [];
        $medications = $user->medications;

        foreach ($medications as $medication) {
            // Gera os horários baseados na regra do medicamento
            $horarios = $medication->getNextDoses($data); 
            
            foreach ($horarios as $hora) {
                // Verifica se este horário específico foi registrado como tomado
                $log = $logsTomados->where('medication_id', $medication->id)
                                ->where('scheduled_time', $hora)
                                ->first();

                $agendaEspelho[] = [
                    'name' => $medication->name,
                    'dosage' => $medication->dosage,
                    'scheduled_time' => $hora,
                    'tomado' => !is_null($log),
                    'taken_at' => $log ? $log->taken_at : null,
                    'take_on_empty_stomach' => $medication->take_on_empty_stomach
                ];
            }
        }

        // Ordena toda a agenda por horário
        usort($agendaEspelho, fn($a, $b) => strcmp($a['scheduled_time'], $b['scheduled_time']));

        return view('doctor.view', compact('diaries', 'agendaEspelho', 'data', 'user'));
    }
}