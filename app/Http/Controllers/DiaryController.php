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
            $categorias = $request->input('categorias');
            
            $query->where(function($q) use ($categorias) {
                foreach ($categorias as $cat) {
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
        
        // LOG DE SEGURANÇA
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $key => $file) {
                \Log::info("Foto $key: " . ($file->isValid() ? 'Válida' : 'Inválida'));
                \Log::info("Tamanho: " . ($file->getSize() / 1024) . " KB");
            }
        } else {
            \Log::info("Nenhum arquivo enviado.");
        }

        $data = $request->validate([
            'entry_datetime' => 'required',
            'content' => 'required',
            'photos' => 'nullable', // Validação flexível para o array ou arquivo único
            'photos.*' => 'image|max:5120'
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
            'photos.*' => 'nullable|image|max:5120'
        ]);

        $currentPhotos = $diary->photos ?? [];

        if ($request->hasFile('photos')) {
            $newPhotos = $this->handlePhotos($request);
            $data['photos'] = array_merge($currentPhotos, $newPhotos);
        } else {
            $data['photos'] = $currentPhotos;
        }

        $diary->update($data);
        return redirect()->route('diaries.index')->with('success', 'Atualizado!');
    }

    private function handlePhotos(Request $request) {
        $paths = [];
        
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            
            // Garante que seja um array, mesmo se o celular enviar um arquivo único
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $paths[] = $file->store('diaries', 'public');
                }
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
            
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function exportPDF(Request $request)
    {
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
        $pdf = Pdf::loadView('diaries.pdf', compact('diaries'));

        return $pdf->stream('relatorio_diario_saude.pdf');
    }

    public function destroy(Diary $diary)
    {
        if ($diary->photos && is_array($diary->photos)) {
            foreach ($diary->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $diary->delete();

        return redirect()->route('diaries.index')->with('success', 'Registro excluído com sucesso!');
    }
}