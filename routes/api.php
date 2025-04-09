<?php

use App\Http\Controllers\ActividadesController;
use App\Http\Controllers\ClasificacionesController;
use App\Http\Controllers\clienteController;
use App\Http\Controllers\CoberturasController;
use App\Http\Controllers\GrupoBarriosController;
use App\Http\Controllers\MigracionBarriosController;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\PropuestasControllerV2;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->post('/propuestas', [ MigracionBarriosController::class, 'callpropuesta' ]);
Route::middleware('auth:api')->post('/editarpuntodeventa', [ UserController::class, 'editarpuntodeventa' ]);
Route::middleware('auth:api')->post('/getPuntos', [ UserController::class, 'getPuntos' ]);
Route::middleware('auth:api')->get('/confirmarpuntodeventa', [ UserController::class, 'confirmarpuntodeventa' ]);

Route::middleware('auth:api')->post('/parametros', [ PropuestaController::class, 'consultaparametros' ]);
Route::middleware('auth:api')->post('/paypro', [ PropuestaController::class, 'paypro' ]);
Route::middleware('auth:api')->get('/usuarios/{codempresa}', [ UsuariosController::class, 'getEmpresa' ]);
Route::middleware('auth:api')->post('/usuarios', [ UsuariosController::class, 'setUsuario' ]);

Route::middleware('auth:api')->post('/propuestas/refpropuesta/{codempresa}', [ PropuestasControllerV2::class, 'getReference' ]);
Route::middleware('auth:api')->post('/propuestas/setreference/{codempresa}', [ PropuestasControllerV2::class, 'setReference' ]);
Route::middleware('auth:api')->post('/propuestas/duplicate-pending', [ PropuestasControllerV2::class, 'duplicatePendingProposal' ]);
Route::post('/propuestas/duplicate', [ PropuestasControllerV2::class, 'duplicateProposal' ]);
Route::get('/propuestas/missing/{date}/{codempresa}/{prefix}', [ PropuestasControllerV2::class, 'getDateProposal' ]);
Route::get('/propuestas/missing/{date}/{codempresa}/{prefix}/{idpropuesta}', [ PropuestasControllerV2::class, 'getDateProposal' ]);
Route::middleware('auth:api')->get('/propuestas/report/{date}/{codempresa}', [ PropuestasControllerV2::class, 'getConsolidated' ]);


Route::middleware('auth:api')->get('/grupobarrios/{codempresa}', [ GrupoBarriosController::class, 'getEmpresa' ]);
Route::middleware('auth:api')->post('/grupobarrios/setgrupobarrios/{codempresa}', [ GrupoBarriosController::class, 'setGrupoBarrios' ]);

Route::middleware('auth:api')->get('/coberturas/{codempresa}', [ CoberturasController::class, 'getEmpresa' ]);
Route::middleware('auth:api')->post('/coberturas/setcoberturas/{codempresa}', [ CoberturasController::class, 'setCobertura' ]);

Route::middleware('auth:api')->get('/clasificaciones/{codempresa}', [ ClasificacionesController::class, 'getEmpresa' ]);
Route::middleware('auth:api')->post('/clasificaciones/setclasificaciones/{codempresa}', [ ClasificacionesController::class, 'setClasificaciones' ]);

Route::middleware('auth:api')->get('/actividades/{codempresa}', [ ActividadesController::class, 'getEmpresa' ]);
Route::middleware('auth:api')->post('/actividades/setactividades/{codempresa}', [ ActividadesController::class, 'setActividades' ]);

Route::middleware('auth:api')->get('/v2/client', [ clienteController::class, 'getClient' ]);
Route::middleware('auth:api')->get('/v2/insured', [ clienteController::class, 'getInsured' ]);

