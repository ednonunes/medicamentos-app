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
        $medications = Auth::user()->medications()->paginate(10);
        
        return view('medications.index', compact('medications'));
    }

    public function create()
    {
        return view('medications.create');
    }
    
    public function store(Request $request)
    {
        try {
            $this->validatePhotos($request);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'dosage' => 'required|string|max:255',
                'interval_hours' => 'nullable|integer|min:1|max:24',
                'start_time' => 'required|date_format:H:i',
                'days_of_week' => 'nullable|array', 
                'days_of_week.*' => 'string',
                'observations' => 'nullable|string',
                'take_on_empty_stomach' => 'boolean', 
                'daily_limit' => 'nullable|integer|min:1',
            ], [
                'interval_hours.required' => 'O campo intervalo (em horas) é obrigatório.',
            ], [
                'interval_hours' => 'intervalo em horas',
                'name' => 'nome do medicamento',
                'dosage' => 'dosagem',
                'start_time' => 'hora inicial',
            ]);

            // Trata o checkbox de jejum caso venha vazio
            $validated['take_on_empty_stomach'] = $request->has('take_on_empty_stomach') ? true : false;

            // Se o usuário desmarcar todos os dias, salva null
            if (!isset($validated['days_of_week'])) {
                $validated['days_of_week'] = null;
            }

            auth()->user()->medications()->create($validated);

            return redirect()->route('medications.index')->with('success', 'Medicamento cadastrado com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Joga os erros de validação de volta para a tela
            throw $e;
        } catch (\Exception $e) {
            // EXIBE O ERRO REAL NA TELA EM VEZ DE APENas PISCAR
            dd('Erro capturado: ' . $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function edit(Medication $medication)
    {
        $medication = Auth::user()->medications()->findOrFail($medication->id);

        return view('medications.edit', compact('medication'));
    }

    public function update(Request $request, $id)
    {
        // Garante de forma segura que o medicamento pertence ao usuário logado
        $medication = Auth::user()->medications()->findOrFail($id);

        $this->validatePhotos($request);

        $validated = $request->validate([
                'name' => 'required|string|max:255',
                'dosage' => 'required|string|max:255',
                'interval_hours' => 'nullable|integer|min:1|max:24',
                'start_time' => 'required|date_format:H:i',
                'days_of_week' => 'nullable|array', 
                'days_of_week.*' => 'string',
                'observations' => 'nullable|string',
                'take_on_empty_stomach' => 'boolean', 
                'daily_limit' => 'nullable|integer|min:1',
            ], [
                'interval_hours.required' => 'O campo intervalo (em horas) é obrigatório.',
            ], [
                'interval_hours' => 'intervalo em horas',
                'name' => 'nome do medicamento',
                'dosage' => 'dosagem',
                'start_time' => 'hora inicial',
            ]);

        // se desmarcar a flag jejjum:
        $validated['take_on_empty_stomach'] = $request->has('take_on_empty_stomach') ? true : false;

        // Se o usuário desmarcar todos os dias, garante que salve null no banco
        if (!isset($validated['days_of_week'])) {
            $validated['days_of_week'] = null;
        }

        $medication->update($validated);

        return redirect()->route('medications.index')->with('success', 'Medicamento atualizado com sucesso!');
    }

    // Remove o medicamento do banco de dados com segurança garantida pela relação do usuário
    public function destroy($id)
    {
        // Busca o medicamento estritamente dentro dos registros do usuário logado
        $medication = Auth::user()->medications()->findOrFail($id);

        // Deleta os logs associados ao medicamento primeiro para evitar erros de chave estrangeira (opcional, mas recomendado)
        $medication->logs()->delete();

        // Deleta o medicamento
        $medication->delete();

        return redirect()->route('medications.index')->with('success', 'Medicamento removido com sucesso!');
    }

    public function agenda(Request $request)
    {
        $user = auth()->user();
        $medications = $user->medications;
        
        $dataSelecionada = $request->input('date', now()->format('Y-m-d'));
        $dataCarbon = \Carbon\Carbon::parse($dataSelecionada);
        
        $agendaDoDia = [];

        $diasTraduzidos = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-feira',
            'Tuesday'   => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday'  => 'Quinta-feira',
            'Friday'    => 'Sexta-feira',
            'Saturday'  => 'Sábado',
        ];

        $diaSemana = $diasTraduzidos[$dataCarbon->format('l')];

        $dosesTomadas = MedicationLog::whereIn('medication_id', $medications->pluck('id'))
            ->whereDate('taken_at', $dataCarbon)
            ->get()
            ->groupBy('medication_id');

        foreach ($medications as $medication) {
            if (!empty($medication->days_of_week) && !in_array($diaSemana, $medication->days_of_week)) {
                continue;
            }

            $doses = $medication->getNextDoses($dataSelecionada);
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

        $doctorLink = route('doctor.view', ['user' => auth()->user()->uuid]);
        
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

    private function validatePhotos(Request $request)
    {
        $request->validate([
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,heic|max:2048',
        ], [
            'photos.*.max' => 'Uma das fotos selecionadas é muito grande. O limite máximo é 2MB por foto.',
            'photos.*.image' => 'O arquivo enviado não é uma imagem válida.',
        ]);
    }
}