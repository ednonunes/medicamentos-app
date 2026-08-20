<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DiaryController extends Controller
{
    public function index(Request $request) 
    {
        $query = auth()->user()->diaries()->latest('entry_datetime');

        if (!$request->hasAny(['categorias', 'data_inicio', 'data_fim'])) {
            $dataInicio = Carbon::today()->format('Y-m-d');
            $dataFim = Carbon::today()->format('Y-m-d');
            $query->whereDate('entry_datetime', $dataInicio);
        } else {
            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');

            if ($dataInicio && !$dataFim) {
                $query->whereDate('entry_datetime', $dataInicio);
            } elseif ($dataInicio && $dataFim) {
                $query->whereBetween('entry_datetime', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);
            } elseif (!$dataInicio && $dataFim) {
                $query->whereDate('entry_datetime', '<=', $dataFim);
            }
        }

        if ($request->has('categorias')) {
            $categorias = $request->input('categorias');
            
            $query->where(function($q) use ($categorias) {
                foreach ($categorias as $cat) {
                    $q->orWhere('content', 'like', '%' . $cat . '%');
                }
            });
        }

        $diaries = $query->get();
        
        return view('diaries.index', compact('diaries', 'dataInicio', 'dataFim'));
    }

    public function create() {
        return view('diaries.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'entry_datetime' => 'required',
            'content' => 'required',
            'photos' => 'nullable', 
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

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        // Se nenhum filtro de data foi passado na rota de exportação, assume o dia de hoje por segurança
        if (!$dataInicio && !$dataFim && !$request->has('categorias')) {
            $dataInicio = Carbon::today()->format('Y-m-d');
            $dataFim = Carbon::today()->format('Y-m-d');
            $query->whereDate('entry_datetime', $dataInicio);
        } else {
            if ($dataInicio && !$dataFim) {
                $query->whereDate('entry_datetime', $dataInicio);
            } elseif ($dataInicio && $dataFim) {
                $query->whereBetween('entry_datetime', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);
            } elseif (!$dataInicio && $dataFim) {
                $query->whereDate('entry_datetime', '<=', $dataFim);
            }
        }

        if ($request->has('categorias')) {
            $categorias = $request->input('categorias');
            $query->where(function($q) use ($categorias) {
                foreach ($categorias as $cat) {
                    $q->orWhere('content', 'like', '%' . $cat . '%');
                }
            });
        }

        $diaries = $query->get();
        
        // Passamos também os filtros para exibir no cabeçalho do PDF
        $pdf = Pdf::loadView('diaries.pdf', compact('diaries', 'dataInicio', 'dataFim'));

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