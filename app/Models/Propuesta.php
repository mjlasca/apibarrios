<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Propuesta extends Model
{
    use HasFactory;

    public function consecutivo(){
        
        $cons = DB::table('propuestas')->where('prefijo','O')->orderBy('reg','DESC')->limit(1)->get();
        if(count($cons) > 0)
            return $cons[0]->reg + 1;
        else
            return 1;
    }

    public function pagarpropuesta($idpropuesta, $prefijo,$formadepago,$idpago,$userpay = "online",$fecha_paga = "", $codempresa = "default"){

        try{
            $propuesta = new Propuesta();

            if($fecha_paga == "")   
                $fecha_paga = date("Y-m-d H:i:s");

            $propuesta->where('reg',$idpropuesta)->where('prefijo',$prefijo)->where('codempresa',$codempresa)->update([
                "codestado" => 1,
                "paga" => 1,
                "csrf" => null,
                "usuariopaga" => $userpay,
                "fecha_paga" => $fecha_paga,
                "tipopago" => $formadepago,
                "compformadepago" => $idpago
            ]);

            $lineapropuesta = new LineasPropuesta();

            $lineapropuesta->where('id_propuesta',$idpropuesta)->where('prefijo',$prefijo)->where('codempresa',$codempresa)->update([
                "codestado" => 1
            ]);

            $pay = new payregistry();
            $pay->idpropuesta = $idpropuesta;
            $pay->prefijo = $prefijo;
            $pay->usuariopaga = $userpay;
            $pay->fecha_paga = $fecha_paga;
            $pay->tipopago = $formadepago;
            $pay->compformadepago = $idpago;
            $pay->save();

            return true;

        }catch(Exception $ex){
            $logs = new Logs();
            $logs->saveerror($ex->getMessage(), "", "", "150");
            
            return false;
        }
    }
}
