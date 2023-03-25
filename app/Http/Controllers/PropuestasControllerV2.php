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
        
        $resultados = Propuesta::whereIn('idpropuesta', array_column($req["registros"], 'idpropuesta'))
                        ->whereIn('prefijo', array_column($req["registros"], 'prefijo'))
                        ->where('codempresa',$codempresa)
                        ->select('prefijo', 'idpropuesta', 'ultmod', 'referencia', 'nota')
                        ->get();

        return response()->json($resultados);
    }

    /**
     * Function update referencia propuestas
     */
    public function setReference($codempresa){

        $req = request()->all();

        /*$resultados = Propuesta::whereIn('idpropuesta', array_column($req["registros"], 'idpropuesta'))
                        ->whereIn('prefijo', array_column($req["registros"], 'prefijo'))
                        ->where('codempresa',$codempresa)
                        ->whereNull('referencia')
                        ->select('prefijo', 'idpropuesta', 'ultmod', 'referencia', 'nota')
                        ->get();
        return response()->json($resultados);*/
        
        try{
            $registros_por_lote = 500;
            $registros_total = count($req["registros"]);
            $num_lotes = ceil($registros_total / $registros_por_lote);
            for ($i = 0; $i < $num_lotes; $i++) {
                $registros_lote = array_slice($req["registros"], $i * $registros_por_lote, $registros_por_lote);
                Propuesta::whereIn('idpropuesta', array_column($registros_lote, 'idpropuesta'))
                            ->whereIn('prefijo', array_column($registros_lote, 'prefijo'))
                            ->where('codempresa', $codempresa)
                            ->whereNull('referencia')
                            ->update([
                                'referencia' => DB::raw("CASE idpropuesta " . $this->construirSentenciaActualizacion($registros_lote, 'referencia') . " END"),
                                'nota' => DB::raw("CASE idpropuesta " . $this->construirSentenciaActualizacion($registros_lote, 'nota') . " END"),
                                'updated_at' => DB::raw('updated_at')
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
