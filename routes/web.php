<?php

use App\Http\Controllers\FormsAuth;
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
Route::get('/', function () {
    $propuesta = new Propuesta();
    //$propuesta = $propuesta->find(70014);
    $propuesta->codempresa = 'MDOIFHJ';
    $propuesta->save();
});
Route::get('/resp', function () {});

Route::get('/propuestas', [ PropuestaController::class, 'callpropuesta' ]);
Route::post('/consultapoliza', [ PropuestaController::class, 'consultapoliza' ]);
Route::get('/descargapdfpoliza', [ PropuestaController::class, 'descargapdfpoliza' ])->name('descargapdfpoliza');
Route::get('/libre-deuda/{id}/{prefijo}', [ PropuestasControllerV2::class, 'descargarPdfLibreDeuda' ])->name('descargarPdfLibreDeuda');
Route::get('/propuesta/agregar-barrios/{prefijo}/{idpropuesta}', [ PropuestasControllerV2::class, 'agregar_barrios' ])->name('agregar_barrios')->middleware('verify.session');
Route::put('/propuesta/agregar-barrios/barrio', [ PropuestasControllerV2::class, 'agregar_barrios_barrio' ])->name('agregar_barrios_barrio');

Route::get('/polizas', [ PropuestaController::class, 'polizas' ])->name('polizas');
Route::get('/cotizadoronline', [ PropuestaController::class, 'cotizadoronline' ]);
Route::post('/savepropuesta', [ PropuestaController::class, 'savepropuesta' ]);
Route::get('/paypropuesta', [ PropuestaController::class, 'paypropuesta' ]);
Route::get('/propuesta-duplicate/pay/{pref}/{id}', [ FormsAuth::class, 'formPay' ]);
Route::post('/webhooksmp', webhookcontroller::class);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
