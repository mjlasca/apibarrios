<?php

use App\Http\Controllers\Catalog\ActividadController;
use App\Http\Controllers\Catalog\BarrioController;
use App\Http\Controllers\Catalog\ClasificacionController;
use App\Http\Controllers\Catalog\CoberturaController;
use App\Http\Controllers\Catalog\ClienteController;
use App\Http\Controllers\Catalog\GrupoBarrioController;
use App\Http\Controllers\FormsAuth;
use App\Http\Controllers\ProposalEditController;
use App\Http\Controllers\ProposalIssueController;
use App\Http\Controllers\ProposalListController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PropuestasControllerV2;
use App\Http\Controllers\webhookcontroller;
use App\Models\Propuesta;
use Illuminate\Support\Facades\Auth;

Auth::routes([
    'register' => false, // Register Routes...
    'reset' => false, // Reset Password Routes...
    'verify' => false, // Email Verification Routes...
]);
Route::get('/', function () {});
Route::get('/resp', function () {});

Route::get('/propuestas', [ PropuestaController::class, 'callpropuesta' ]);
Route::post('/consultapoliza', [ PropuestaController::class, 'consultapoliza' ]);
Route::get('/descargapdfpoliza', [ PropuestaController::class, 'descargapdfpoliza' ])->name('descargapdfpoliza');
Route::get('/descargaseguro/{id}/{prefijo}', [ PropuestasControllerV2::class, 'downloadAll' ])->name('downloadAll');
Route::get('/libre-deuda/{id}/{prefijo}', [ PropuestasControllerV2::class, 'descargarPdfLibreDeuda' ])->name('descargarPdfLibreDeuda');
Route::get('/propuesta/agregar-barrios/{prefijo}/{idpropuesta}', [ PropuestasControllerV2::class, 'agregar_barrios' ])->name('agregar_barrios')->middleware('verify.session');
Route::put('/propuesta/agregar-barrios/barrio', [ PropuestasControllerV2::class, 'agregar_barrios_barrio' ])->name('agregar_barrios_barrio');

Route::get('/polizas', [ PropuestaController::class, 'polizas' ])->name('polizas');
Route::get('/cotizadoronline', [ PropuestaController::class, 'cotizadoronline' ]);
Route::post('/savepropuesta', [ PropuestaController::class, 'savepropuesta' ]);
Route::get('/paypropuesta', [ PropuestaController::class, 'paypropuesta' ]);
Route::get('/propuesta-duplicate/pay/{pref}/{id}', [ FormsAuth::class, 'formPay' ]);
Route::post('/webhooksmp', webhookcontroller::class);

Route::prefix('propuesta/emision')->middleware('auth')->group(function () {
    Route::get('/', [ProposalIssueController::class, 'create'])->name('propuesta.emision');
    Route::get('/clientes/search', [ProposalIssueController::class, 'searchClients']);
    Route::get('/clientes/resolve/{documento}', [ProposalIssueController::class, 'resolveClient']);
    Route::get('/actividades/{actividad}/clasificaciones', [ProposalIssueController::class, 'classificationsByActivity']);
    Route::post('/clientes/save', [ProposalIssueController::class, 'saveClient']);
    Route::post('/store', [ProposalIssueController::class, 'store'])->name('propuesta.emision.store');
});

Route::prefix('informes')->middleware('auth')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/generar', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/descargar/fin-del-dia', [ReportController::class, 'downloadFinDelDia'])->name('reports.download.fin_del_dia');
    Route::get('/descargar/envio-colectivo', [ReportController::class, 'downloadEnvioColectivo'])->name('reports.download.envio_colectivo');
    Route::get('/descargar/envio-individual', [ReportController::class, 'downloadEnvioIndividual'])->name('reports.download.envio_individual');
});

Route::prefix('propuesta')->middleware('auth')->group(function () {
    Route::get('/listar', [ProposalListController::class, 'index'])->name('propuesta.listar');
    Route::get('/{propuesta}/editar', [ProposalEditController::class, 'edit'])->name('propuesta.editar');
    Route::put('/{propuesta}', [ProposalEditController::class, 'update'])->name('propuesta.update');
    Route::post('/{propuesta}/anular', [ProposalEditController::class, 'cancel'])->name('propuesta.cancelar');
    Route::post('/pagar', [ProposalEditController::class, 'pay'])->name('propuesta.pagar');
});

Route::prefix('catalogo')->middleware('auth')->group(function () {
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::post('/clientes/{id}/anular', [ClienteController::class, 'deactivate'])->name('clientes.deactivate');

    Route::get('/coberturas', [CoberturaController::class, 'index'])->name('coberturas.index');
    Route::get('/coberturas/crear', [CoberturaController::class, 'create'])->name('coberturas.create');
    Route::post('/coberturas', [CoberturaController::class, 'store'])->name('coberturas.store');
    Route::get('/coberturas/{id}/editar', [CoberturaController::class, 'edit'])->name('coberturas.edit');
    Route::put('/coberturas/{id}', [CoberturaController::class, 'update'])->name('coberturas.update');
    Route::post('/coberturas/{id}/anular', [CoberturaController::class, 'deactivate'])->name('coberturas.deactivate');

    Route::get('/actividades', [ActividadController::class, 'index'])->name('actividades.index');
    Route::get('/actividades/crear', [ActividadController::class, 'create'])->name('actividades.create');
    Route::post('/actividades', [ActividadController::class, 'store'])->name('actividades.store');
    Route::get('/actividades/{id}/editar', [ActividadController::class, 'edit'])->name('actividades.edit');
    Route::put('/actividades/{id}', [ActividadController::class, 'update'])->name('actividades.update');
    Route::post('/actividades/{id}/anular', [ActividadController::class, 'deactivate'])->name('actividades.deactivate');

    Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
    Route::get('/clasificaciones/crear', [ClasificacionController::class, 'create'])->name('clasificaciones.create');
    Route::post('/clasificaciones', [ClasificacionController::class, 'store'])->name('clasificaciones.store');
    Route::get('/clasificaciones/{id}/editar', [ClasificacionController::class, 'edit'])->name('clasificaciones.edit');
    Route::put('/clasificaciones/{id}', [ClasificacionController::class, 'update'])->name('clasificaciones.update');
    Route::post('/clasificaciones/{id}/anular', [ClasificacionController::class, 'deactivate'])->name('clasificaciones.deactivate');

    Route::get('/barrios', [BarrioController::class, 'index'])->name('barrios.index');
    Route::get('/barrios/crear', [BarrioController::class, 'create'])->name('barrios.create');
    Route::post('/barrios', [BarrioController::class, 'store'])->name('barrios.store');
    Route::get('/barrios/{id}/editar', [BarrioController::class, 'edit'])->name('barrios.edit');
    Route::put('/barrios/{id}', [BarrioController::class, 'update'])->name('barrios.update');
    Route::post('/barrios/{id}/anular', [BarrioController::class, 'deactivate'])->name('barrios.deactivate');

    Route::get('/grupos-barrios', [GrupoBarrioController::class, 'index'])->name('grupos-barrios.index');
    Route::get('/grupos-barrios/crear', [GrupoBarrioController::class, 'create'])->name('grupos-barrios.create');
    Route::post('/grupos-barrios', [GrupoBarrioController::class, 'store'])->name('grupos-barrios.store');
    Route::get('/grupos-barrios/{id}/editar', [GrupoBarrioController::class, 'edit'])->name('grupos-barrios.edit');
    Route::put('/grupos-barrios/{id}', [GrupoBarrioController::class, 'update'])->name('grupos-barrios.update');
    Route::post('/grupos-barrios/{id}/anular', [GrupoBarrioController::class, 'deactivate'])->name('grupos-barrios.deactivate');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
