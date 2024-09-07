<?php

namespace App\Http\Controllers;

use App\Models\gruposbarrio;
use Illuminate\Http\Request;

class GrupoBarriosController extends Controller
{
    public function getEmpresa($codempresa){
        $coberturas = gruposbarrio::groupBy('id','idbarrio')->get();
        return response()->json($coberturas);
    }
}
