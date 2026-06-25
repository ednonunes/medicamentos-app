<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{    
    public function index()
    {
        $user = auth()->user();
        
        $dadosUrina = Diary::where('user_id', $user->id)
            ->where('entry_datetime', '>=', now()->subDays(7))
            ->where('content', 'LIKE', '%Urina%')
            ->get()
            ->groupBy(function($item) {
                // Agrupa pelo formato d/m para somar tudo que for do mesmo dia
                return $item->entry_datetime->format('d/m');
            })
            ->map(function ($group, $date) {
                return [
                    'data' => $date,
                    // Soma todos os valores encontrados naquele dia
                    'valor' => $group->sum(function($item) {
                        preg_match('/Urina:\s*(\d+)/i', $item->content, $matches);
                        return isset($matches[1]) ? (int)$matches[1] : 0;
                    }),
                    'dia_semana' => Carbon::createFromFormat('d/m', $date)->locale('pt_BR')->isoFormat('ddd')
                ];
            })
            ->values(); // Reseta os índices para o gráfico funcionar corretamente

        return view('dashboard', compact('dadosUrina'));
    }
}