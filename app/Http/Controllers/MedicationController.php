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
    
    // As outras funções (store, edit, etc) faremos a seguir
}