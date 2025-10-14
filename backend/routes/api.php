<?php

use App\Domain\Entity\Ordem\Entidade;
use App\Http\Authentication;
use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Models\OrdemModel;
use Illuminate\Support\Facades\Route;

Route::get('ping', fn() => response()->json([
    'err' => false,
    'msg' => 'pong'
]))->withoutMiddleware(JsonWebTokenMiddleware::class);

require_once __DIR__ . '/usuario.php';
require_once __DIR__ . '/servico.php';
require_once __DIR__ . '/material.php';
require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/veiculo.php';
require_once __DIR__ . '/ordem.php';

Route::fallback(fn() => response()->json([
    'err' => true,
    'msg' => 'Recurso não encontrado',
]));


// auth

Route::post('auth/login', [Authentication::class, 'authenticate'])->withoutMiddleware(JsonWebTokenMiddleware::class);

Route::withoutMiddleware(JsonWebTokenMiddleware::class)->get('/media-exec-ordem', function () {
    $ordens = OrdemModel::query()
        ->where('status', Entidade::STATUS_FINALIZADA)
        ->whereNotNull('dt_abertura')
        ->whereNotNull('dt_finalizacao')
        ->select(['dt_abertura', 'dt_finalizacao'])
        ->get();

    if ($ordens->isEmpty()) {
        return [
            'total_ordens_finalizadas' => 0,
            'tempo_medio_horas' => 0,
            'tempo_medio_dias' => 0,
            'tempo_medio_formatado' => '0 dias, 0 horas'
        ];
    }

    $totalSegundos = 0;

    foreach ($ordens as $ordem) {
        $dtAbertura = \Carbon\Carbon::parse($ordem->dt_abertura);
        $dtFinalizacao = \Carbon\Carbon::parse($ordem->dt_finalizacao);

        $totalSegundos += $dtAbertura->diffInSeconds($dtFinalizacao);
    }

    $tempoMedioSegundos = $totalSegundos / $ordens->count();
    $tempoMedioHoras = $tempoMedioSegundos / 3600;
    $tempoMedioDias = $tempoMedioHoras / 24;

    $dias = floor($tempoMedioDias);
    $horas = floor($tempoMedioHoras % 24);

    return [
        'total_ordens_finalizadas' => $ordens->count(),
        'tempo_medio_horas' => round($tempoMedioHoras, 2),
        'tempo_medio_dias' => round($tempoMedioDias, 2),
        'tempo_medio_formatado' => "{$dias} dias, {$horas} horas"
    ];
});
