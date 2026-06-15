<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationController extends Controller
{
    // Esta função responde ao link "Medicamentos" que você criou no menu
    public function index()
    {
        // Pega os remédios do usuário logado
        $medications = Auth::user()->medications;

        // Retorna uma tela específica para a listagem (CRUD)
        return view('medications.index', compact('medications'));
    }

    public function create()
    {
        return view('medications.create');
    }
    
    // Recebe os dados do formulário e grava no banco 
    public function store(Request $request)
    {
        // 1. Validação por motivos de segurança
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'interval_hours' => 'required|integer|min:1',
            'start_time' => 'required',
        ]);

        // 2. Grava usando o relacionamento (insere o user_id automaticamente)
        Auth::user()->medications()->create($validated);

        // 3. Redireciona de volta para a listagem com uma mensagem de sucesso
        return redirect()->route('medications.index')->with('success', 'Medicamento cadastrado com sucesso!');
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
}