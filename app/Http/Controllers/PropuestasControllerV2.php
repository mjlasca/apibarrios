<?php

namespace App\Http\Controllers;

use App\Models\Propuesta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;



class PropuestasControllerV2 extends Controller
{
    //
    public function getReference($codempresa){

        $req = request()->all();
        
        $resultados = Propuesta::query()
                    ->whereIn('idpropuesta', collect($req['registros'])->pluck('idpropuesta'))
                    ->whereIn('prefijo', collect($req['registros'])->pluck('prefijo'))
                    ->where('codempresa', $codempresa)
                    ->whereNotNull('referencia')
                    ->select('prefijo', 'idpropuesta', 'ultmod', 'referencia', 'nota', 'prima')
                    ->get();


        return response()->json($resultados);
    }


    
    public function setReference($codempresa){

        $req = request()->all();
    
        try{
            foreach ($req["registros"] as $registro) {
                Propuesta::where('idpropuesta', $registro['idpropuesta'])
                            ->where('prefijo', $registro['prefijo'])
                            ->where('codempresa', $codempresa)
                            ->whereNull('referencia')
                            ->update([
                                'referencia' => $registro['referencia'],
                                'nota' => $registro['nota'],
                                'prima' => $registro['prima'],
                            ]);
            }
    
            return response()->json(["success" => TRUE]);
    
        }catch(Exception $ex){
    
            return response()->json(["success" => FALSE, "msg" => $ex->getMessage()]);
        }   
    }
    

    function construirSentenciaActualizacion($registros, $campo) {
        $sentencia = "";
        foreach ($registros as $registro) {
            $sentencia .= "WHEN " . $registro['idpropuesta'] . " THEN '" . $registro[$campo] . "' ";
        }
        return $sentencia;
    }
    
}
