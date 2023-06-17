<?php

namespace App\Http\Controllers;

use App\Models\Cobertura;
use App\Models\PendingDuplicate;
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


    public function getDuplicate(Request $req){
        
        try{
            $data = [];

            if((isset($req['pref']) && isset( $req['id']) ) && ($req['pref'] != '' && $req['id'] != '')){
                $prefijo = $req['pref'];
                $idPropuesta = $req['id'];
                $data = Propuesta::query()
                ->where(function ($query) use ($prefijo, $idPropuesta) {
                    $query->where('prefijo', $prefijo)
                        ->where('idpropuesta', $idPropuesta);
                })->get();    
            }
    
            if(isset($req['ref']) && $req['ref'] != "" ){
                $referencia = $req['ref'];
                $data = Propuesta::query()
                        ->where(function ($query) use ($referencia) {
                            $query->where('referencia', $referencia);
                        })->get();
            }
            

            if(count($data) > 0){

                $total = 0;
                $cobertura = Cobertura::query()->where('nombre',$data[0]->id_cobertura)->get();
                if(count($cobertura) > 0){
                    $total = $cobertura[0]->vrMensual * $req['monthly'] * $data[0]->num_polizas ;
                }

                $duplicate = PendingDuplicate::updateOrCreate(
                    ['prefijo' => $data[0]->prefijo, 'idpropuesta' => $data[0]->idpropuesta],
                    [
                        'idpropuesta' => $data[0]->idpropuesta,
                        'prefijo' => $data[0]->prefijo,
                        'meses' => $req['monthly'],
                        'premio' => $cobertura[0]->vrMensual,
                        'premio_total' => $total
                    ]
                );

                $res = [
                    'total' => $total,
                    'banco' => 'Banco de la república',
                    'cuenta' => '1000000001'
                ];

                return response()->json(["success" => TRUE, "data" => $res]);
            }

            return response()->json(["success" => FALSE]);
            

        }catch(Exception $ex){
    
            return response()->json(["success" => FALSE, "msg" => $ex->getMessage()]);
        }   
        
    }
    
}
