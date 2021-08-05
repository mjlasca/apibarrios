<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropuestaController;
use SebastianBergmann\CodeUnit\FunctionUnit;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    

    //return view('welcome');
});

Route::get('/propuestas', [ PropuestaController::class, 'callpropuesta' ]);
Route::post('/consultapoliza', [ PropuestaController::class, 'consultapoliza' ]);
Route::get('/descargapdfpoliza', [ PropuestaController::class, 'descargapdfpoliza' ]);

Route::get('/polizas', function(){
    $data = [];
    $success = true;
    return view('polizas.index', compact('data','success'));
});


Route::get('/cotizadoronline', [ PropuestaController::class, 'cotizadoronline' ]);



