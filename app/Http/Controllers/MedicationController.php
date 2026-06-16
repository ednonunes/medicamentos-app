<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\MedicationLog;

class MedicationController extends Controller
{
    // Listagem dos medicamentos
    public function index()
    {
        // Substituímos o ->medications por uma consulta com paginação (10 por página)
        $medications = Auth::user()->medications()->paginate(10);
        
        return view('medications.index', compact('medications'));
    }

    public function create()
    {
        return view('medications.create');
    }
    
    // Recebe os dados do formulário e grava no banco 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'interval_hours' => 'required|integer|min:1|max:24',
            'start_time' => 'required|date_format:H:i',
            'days_of_week' => 'nullable|array', // 👈 Valida como array opcional
            'days_of_week.*' => 'string',
        ]);

        auth()->user()->medications()->create($validated);

        return redirect()->route('medications.index')->with('success', 'Medicamento cadastrado com sucesso!');
    }

    // Busca o medicamento para exibir no formulário de edição
    public function edit(Medication $medication)
    {
        // Trava de segurança contra manipulação de URL
        if ($medication->user_id !== Auth::id()) {
            abort(403, 'Ação não autorizada.');
        }

        return view('medications.edit', compact('medication'));
    }

    public function update(Request $request, Medication $medication)
    {
        $this->authorize('update', $medication);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'interval_hours' => 'required|integer|min:1|max:24',
            'start_time' => 'required|date_format:H:i',
            'days_of_week' => 'nullable|array', // 👈 Valida como array opcional
            'days_of_week.*' => 'string',
        ]);

        // Se o usuário desmarcar todos os dias, garante que salve null no banco
        if (!isset($validated['days_of_week'])) {
            $validated['days_of_week'] = null;
        }

        $medication->update($validated);

        return redirect()->route('medications.index')->with('success', 'Medicamento atualizado com sucesso!');
    }

    // Remove o medicamento do banco de dados
    public function destroy(Medication $medication)
    {
        // Segurança extra: Garante que o medicamento realmente pertence ao usuário logado
        if ($medication->user_id !== Auth::id()) {
            abort(403, 'Ação não autorizada.');
        }

        // Deleta o registro do banco de dados
        $medication->delete();

        // Redireciona de volta com mensagem de sucesso
        return redirect()->route('medications.index')->with('success', 'Medicamento removido com sucesso!');
    }

    public function agenda()
    {
        $user = auth()->user();
        $medications = $user->medications;
        $agendaDoDia = [];

        // Array de tradução para converter o formato 'l' do Carbon (inglês) para português
        $diasTraduzidos = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-feira',
            'Tuesday'   => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday'  => 'Quinta-feira',
            'Friday'    => 'Sexta-feira',
            'Saturday'  => 'Sábado',
        ];

        // Pega o dia de hoje em inglês e converte para o equivalente em português
        $diaIngles = now()->format('l'); 
        $hojeEmPortugues = $diasTraduzidos[$diaIngles];

        $dosesTomadasHoje = MedicationLog::whereIn('medication_id', $medications->pluck('id'))
            ->whereDate('taken_at', \Carbon\Carbon::today())
            ->get()
            ->groupBy('medication_id');

        foreach ($medications as $medication) {
            
            // Se o remédio tiver dias específicos e hoje não for um deles, pula
            if (!empty($medication->days_of_week) && !in_array($hojeEmPortugues, $medication->days_of_week)) {
                continue;
            }

            $doses = $medication->getNextDoses();
            $logsDoRemedio = $dosesTomadasHoje->get($medication->id, collect());

            foreach ($doses as $hora) {
                $jaTomado = $logsDoRemedio->contains('scheduled_time', $hora);

                $agendaDoDia[] = [
                    'id' => $medication->id,
                    'name' => $medication->name,
                    'dosage' => $medication->dosage,
                    'hora' => $hora,
                    'ja_passou' => \Carbon\Carbon::createFromFormat('H:i', $hora)->isBefore(now()),
                    'ja_tomado' => $jaTomado,
                    // Passamos os dias salvos para usar na View se necessário
                    'days_of_week' => $medication->days_of_week, 
                ];
            }
        }

        usort($agendaDoDia, function ($a, $b) {
            return strcmp($a['hora'], $b['hora']);
        });

        return view('medications.agenda', compact('agendaDoDia'));
    }

    /**
     * Registra que o medicamento foi tomado.
     */
    public function takeDose(Request $request)
    {
        $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'scheduled_time' => 'required|string',
        ]);

        // Segurança: Garante que o medicamento realmente pertence ao usuário logado
        $medication = auth()->user()->medications()->findOrFail($request->medication_id);

        // Evita duplicar o registro caso o usuário clique duas vezes rápido
        $jaExiste = MedicationLog::where('medication_id', $medication->id)
            ->where('scheduled_time', $request->scheduled_time)
            ->whereDate('taken_at', Carbon::today())
            ->exists();

        if (!$jaExiste) {
            MedicationLog::create([
                'medication_id' => $medication->id,
                'scheduled_time' => $request->scheduled_time,
                'taken_at' => now(),
            ]);
        }

        return redirect()->route('medications.agenda')->with('success', 'Dose registrada com sucesso!');
    }
}