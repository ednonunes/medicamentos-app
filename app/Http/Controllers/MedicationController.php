<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\MedicationLog;
use Illuminate\Support\Facades\URL;

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
            'days_of_week' => 'nullable|array', 
            'days_of_week.*' => 'string',
            'observations' => 'nullable|string',
            'take_on_empty_stomach' => 'boolean', 
            'daily_limit' => 'nullable|integer|min:1',
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
            'days_of_week' => 'nullable|array', 
            'days_of_week.*' => 'string',
            'observations' => 'nullable|string',
            'take_on_empty_stomach' => 'boolean', 
            'daily_limit' => 'nullable|integer|min:1',
        ]);

        // se desmarcar a flag jejjum:
        $validated['take_on_empty_stomach'] = $request->has('take_on_empty_stomach') ? true : false;

        // Se o usuário desmarcar todos os dias, garante que salve null no banco
        if (!isset($validated['days_of_week'])) {
            $validated['days_of_week'] = null;
        }

        $medication->update($validated);

        return redirect()->route('medications.index')->with('success', 'Medicamento actualizado com sucesso!');
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

    public function agenda(Request $request)
    {
        $user = auth()->user();
        $medications = $user->medications;
        
        // 1. Captura a data selecionada ou usa hoje como padrão
        $dataSelecionada = $request->input('date', now()->format('Y-m-d'));
        $dataCarbon = \Carbon\Carbon::parse($dataSelecionada);
        
        $agendaDoDia = [];

        // Array de tradução
        $diasTraduzidos = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-feira',
            'Tuesday'   => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday'  => 'Quinta-feira',
            'Friday'    => 'Sexta-feira',
            'Saturday'  => 'Sábado',
        ];

        // Converte a data selecionada para o dia da semana correspondente
        $diaSemana = $diasTraduzidos[$dataCarbon->format('l')];

        // Busca doses tomadas na data selecionada
        $dosesTomadas = MedicationLog::whereIn('medication_id', $medications->pluck('id'))
            ->whereDate('taken_at', $dataCarbon)
            ->get()
            ->groupBy('medication_id');

        foreach ($medications as $medication) {
            if (!empty($medication->days_of_week) && !in_array($diaSemana, $medication->days_of_week)) {
                continue;
            }

            $doses = $medication->getNextDoses($dataSelecionada); // Passando a data do filtro
            if ($medication->daily_limit && count($doses) > $medication->daily_limit) {
                $doses = array_slice($doses, 0, $medication->daily_limit);
            }
            $logsDoRemedio = $dosesTomadas->get($medication->id, collect());

            foreach ($doses as $hora) {
                $jaTomado = $logsDoRemedio->contains('scheduled_time', $hora);

                $agendaDoDia[] = [
                    'id' => $medication->id,
                    'name' => $medication->name,
                    'dosage' => $medication->dosage,
                    'hora' => $hora,
                    'ja_passou' => $dataCarbon->isToday() ? \Carbon\Carbon::createFromFormat('H:i', $hora)->isBefore(now()) : $dataCarbon->isPast(),
                    'ja_tomado' => $jaTomado,
                    'days_of_week' => $medication->days_of_week, 
                ];
            }
        }

        usort($agendaDoDia, function ($a, $b) {
            return strcmp($a['hora'], $b['hora']);
        });

        // Gera link assinado mantendo a data
        $doctorLink = URL::signedRoute('doctor.view', [
            'user' => $user->id,
            'date' => $dataSelecionada
        ]);

        return view('medications.agenda', compact('agendaDoDia', 'doctorLink', 'dataSelecionada'));
    }

    public function takeDose(Request $request)
    {
        $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'scheduled_time' => 'required|string',
            'date' => 'required|date',
        ]);

        $medication = auth()->user()->medications()->findOrFail($request->medication_id);
        $dataSelecionada = $request->date;

        $jaExiste = MedicationLog::where('medication_id', $medication->id)
            ->where('scheduled_time', $request->scheduled_time)
            ->whereDate('taken_at', $dataSelecionada)
            ->exists();

        if (!$jaExiste) {
            MedicationLog::create([
                'medication_id' => $medication->id,
                'scheduled_time' => $request->scheduled_time,
                'taken_at' => $dataSelecionada . ' ' . now()->format('H:i:s'),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Dose registrada!']);
    }

    public function undo(Request $request)
    {
        $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'scheduled_time' => 'required|string',
            'date' => 'required|date',
        ]);

        $medication = auth()->user()->medications()->findOrFail($request->medication_id);
        $dataSelecionada = $request->date;

        $log = MedicationLog::where('medication_id', $medication->id)
            ->where('scheduled_time', $request->scheduled_time)
            ->whereDate('taken_at', $dataSelecionada)
            ->first();

        if ($log) {
            $log->delete();
        }

        return response()->json(['success' => true, 'message' => 'Registro desfeito!']);
    }

}