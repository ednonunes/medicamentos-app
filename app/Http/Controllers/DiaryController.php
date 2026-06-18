<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DiaryController extends Controller
{
    public function index(Request $request) 
    {
        $query = auth()->user()->diaries()->latest('entry_datetime');

        if ($request->has('categorias')) {
            $categorias = $request->input('categorias'); // Recebe um array
            
            $query->where(function($q) use ($categorias) {
                foreach ($categorias as $cat) {
                    // Filtra registos que contêm pelo menos uma das categorias selecionadas
                    $q->orWhere('content', 'like', '%' . $cat . '%');
                }
            });
        }

        $diaries = $query->get();
        
        return view('diaries.index', compact('diaries'));
    }

    public function create() {
        return view('diaries.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'entry_datetime' => 'required',
            'content' => 'required',
            'photos' => 'nullable|array|max:3', // Garante no máximo 3 itens no array
            'photos.*' => 'image|max:2048'      // Valida cada foto individualmente
        ]);

        $data['photos'] = $this->handlePhotos($request);

        auth()->user()->diaries()->create($data);
        return redirect()->route('diaries.index')->with('success', 'Registro criado!');
    }

    public function edit(Diary $diary) {
        return view('diaries.edit', compact('diary'));
    }

    public function update(Request $request, Diary $diary) {
        $data = $request->validate([
            'entry_datetime' => 'required',
            'content' => 'required',
            'photos.*' => 'nullable|image|max:2048'
        ]);

        // 1. Recupera as fotos atuais (garante que é um array, mesmo se estiver vazio)
        $currentPhotos = $diary->photos ?? [];

        // 2. Se houver novas fotos, processa e mescla com as existentes
        if ($request->hasFile('photos')) {
            $newPhotos = $this->handlePhotos($request);
            
            // Mescla o array antigo com o novo
            $data['photos'] = array_merge($currentPhotos, $newPhotos);
        } else {
            // Se não houver novas fotos, mantém as que já existiam
            $data['photos'] = $currentPhotos;
        }

        $diary->update($data);
        return redirect()->route('diaries.index')->with('success', 'Atualizado!');
    }

    // Método auxiliar para não repetir código
    private function handlePhotos(Request $request) {
        $paths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $paths[] = $file->store('diaries', 'public');
            }
        }
        return $paths;
    }

    public function deletePhoto(Diary $diary, $index)
    {
        $photos = $diary->photos;


        if (isset($photos[$index])) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photos[$index]);
            unset($photos[$index]);
            $diary->photos = array_values($photos);
            $diary->save();
            
            return response()->json(['success' => true]); // AJAX precisa disso
        }

        return response()->json(['success' => false], 404);
    }

    public function exportPDF(Request $request)
    {
        // Pega os mesmos filtros usados na listagem
        $query = auth()->user()->diaries()->latest('entry_datetime');

        if ($request->has('categorias')) {
            $categorias = $request->input('categorias');
            $query->where(function($q) use ($categorias) {
                foreach ($categorias as $cat) {
                    $q->orWhere('content', 'like', '%' . $cat . '%');
                }
            });
        }

        $diaries = $query->get();

        // Carrega a view do PDF
        $pdf = Pdf::loadView('diaries.pdf', compact('diaries'));

        return $pdf->stream('relatorio_diario_saude.pdf');
    }
}