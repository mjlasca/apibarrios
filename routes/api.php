<?php

use App\Http\Controllers\MigracionBarriosController;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\PropuestasControllerV2;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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

