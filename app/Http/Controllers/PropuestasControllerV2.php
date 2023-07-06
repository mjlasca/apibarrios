<?php

namespace App\Http\Controllers;

use App\Models\Cobertura;
use App\Models\PendingDuplicate;
use App\Models\Propuesta;
use Barryvdh\DomPDF\Facade as PDF;
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


    public function duplicatePendingProposal(Request $req){
        
        try{

            $proposal = new Propuesta();
            $data = $proposal->validateProposal($req);

            if(count($data) > 0){
                
                $resp = $proposal->calculateProposedTotal($data[0]->prefijo, $data[0]->idpropuesta, $req['monthly']);
                
                if(count( $resp ) > 0){

                    $duplicate = PendingDuplicate::updateOrCreate(
                        ['prefijo' => $data[0]->prefijo, 'idpropuesta' => $data[0]->idpropuesta, 'status' => 0],
                        [
                            'idpropuesta' => $data[0]->idpropuesta,
                            'prefijo' => $data[0]->prefijo,
                            'meses' => $req['monthly'],
                            'premio' => $resp['vrunit'],
                            'premio_total' => $resp['total'],
                        ]
                    );
    
                    return response()->json(["success" => TRUE, "data" => $resp]);
                }
                
            }

            return response()->json(["success" => FALSE, 'msg' => 'No hay coincidencia con la póliza']);
            

        }catch(Exception $ex){
    
            return response()->json(["success" => FALSE, "msg" => $ex->getMessage()]);
        }   
        
    }


    public function duplicateProposal(Request $req){
        try{

            $regpending = PendingDuplicate::where('prefijo', $req['pref'])->where('idpropuesta', $req['id'])->where('status',0)->first();
            
                if( $regpending ){
                    
                    $prop = new Propuesta();
                    
                    $data= [
                        'meses' => $regpending->meses,
                        'premio' => $regpending->premio,
                        'premio_total' => $regpending->premio_total,
                        'pref' => $req['pref'],
                        'id' => $req['id'],
                        'forma_pago' => $req['forma_pago'],
                        'nro_comprobante' => $req['nro_comprobante'],
                    ];
                    
                    $resDuplicate = $prop->duplicate('O', $data);
                    
                    if($resDuplicate){
                        $regpending->status = 1; 
                        $regpending->save();

                        return redirect()->route('descargapdfpoliza', [
                            'id' => $resDuplicate->idpropuesta,
                            'prefijo' => $resDuplicate->prefijo
                        ]);

                    }

                     //response()->json(["success" => TRUE, "data" => $resp]);
                }
                
                return response()->json(["success" => FALSE, 'msg' => 'No hay coincidencia con la póliza']);

        }catch(Exception $ex){

            return response()->json(["success" => FALSE, "msg" => $ex->getMessage()]);
            
        }   
    }

    
}
