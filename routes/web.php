<?php

use App\Http\Controllers\FormsAuth;
use App\Http\Controllers\ProposalEditController;
use App\Http\Controllers\ProposalIssueController;
use App\Http\Controllers\ProposalListController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropuestaController;
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

Route::prefix('propuesta')->middleware('auth')->group(function () {
    Route::get('/listar', [ProposalListController::class, 'index'])->name('propuesta.listar');
    Route::get('/{propuesta}/editar', [ProposalEditController::class, 'edit'])->name('propuesta.editar');
    Route::put('/{propuesta}', [ProposalEditController::class, 'update'])->name('propuesta.update');
    Route::post('/{propuesta}/anular', [ProposalEditController::class, 'cancel'])->name('propuesta.cancelar');
    Route::post('/pagar', [ProposalEditController::class, 'pay'])->name('propuesta.pagar');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
