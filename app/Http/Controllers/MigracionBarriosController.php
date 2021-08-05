<?php

namespace App\Http\Controllers;

use App\Models\Actividade;
use App\Models\BarriosPropuesta;
use App\Models\Clasificacione;
use App\Models\Cobertura;
use App\Models\LineasPropuesta;
use Illuminate\Http\Request;
use App\Models\Propuesta;
use Illuminate\Support\Facades\DB;

class MigracionBarriosController extends Controller
{
    //
    public function callpropuesta()
    {
        $req = request()->all();
        $result = $this->savepropuesta($req);
        if ($result != "")
            return response()->json(['res' => 'Hubo errores al subir la información ' . $result], 400);
        else
            return response()->json(['res' => 'Se han subido todos los datos de migracion con exito'], 200);
    }

    private function savepropuesta($req)
    {

        
        $errores = "";
        if ($req["listpropuestas"] != null) {
            foreach ($req["listpropuestas"] as $value) {
                $propuesta = new Propuesta();

                $propuesta->documento = $value["documento"];
                $propuesta->reg = $value["id"];
                if (isset($value["nombre"]))
                    $propuesta->nombre = $value["nombre"];
                $propuesta->num_polizas = $value["num_polizas"];
                $propuesta->meses = $value["meses"];
                $propuesta->id_cobertura = $value["id_cobertura"];
                $propuesta->id_barrio = $value["id_barrio"];
                $propuesta->nueva_poliza = $value["nueva_poliza"];
                $propuesta->premio = $value["premio"];
                $propuesta->premio_total = $value["premio_total"];
                $propuesta->fechaDesde = $value["fechaDesde"];
                $propuesta->fechaHasta = $value["fechaHasta"];
                $propuesta->clausula = $value["clausula"];
                $propuesta->barrio_beneficiario = $value["barrio_beneficiario"];
                $propuesta->ultmod = $value["ultmod"];
                $propuesta->useredit = $value["user_edit"];
                $propuesta->codestado = $value["codestado"];
                $propuesta->cobertura_suma = $value["cobertura_suma"];
                $propuesta->cobertura_deducible = $value["cobertura_deducible"];
                $propuesta->cobertura_gastos = $value["cobertura_gastos"];
                $propuesta->promocion = $value["promocion"];
                $propuesta->paga = $value["paga"];
                $propuesta->fecha_paga = $value["fecha_paga"];
                $propuesta->referencia = $value["referencia"];
                $propuesta->prima = $value["prima"];
                $propuesta->master = $value["master"];
                $propuesta->organizador = $value["organizador"];
                $propuesta->productor = $value["productor"];
                if (isset($value["puntodeventa"]))
                    $propuesta->puntodeventa = $value["puntodeventa"];
                if (isset($value["prefijo"]))
                    $propuesta->prefijo = $value["prefijo"];

                if (!$propuesta->save()) {
                    $errores .= "No se pudo guardar " . $propuesta->reg;
                }
            }
        }

        

        if ($req["listlineaspropuestas"] != null) {
            foreach ($req["listlineaspropuestas"] as $value) {
                $lineaspropuestas = new LineasPropuesta();
                $lineaspropuestas->reg = $value["id"];
                $lineaspropuestas->id_propuesta = $value["id_propuesta"];
                $lineaspropuestas->documento = $value["documento"];
                $lineaspropuestas->tipo_documento = $value["tipo_documento"];
                $lineaspropuestas->apellidos = $value["apellidos"];
                $lineaspropuestas->nombres = $value["nombres"];
                $lineaspropuestas->fecha_nacimiento = $value["fecha_nacimiento"];
                $lineaspropuestas->id_actividad = $value["id_actividad"];
                $lineaspropuestas->id_clasificacion = $value["id_clasificacion"];
                $lineaspropuestas->premio = $value["premio"];
                $lineaspropuestas->ultmod = $value["ultmod"];
                $lineaspropuestas->user_edit = $value["user_edit"];
                $lineaspropuestas->codestado = $value["codestado"];
                $lineaspropuestas->fechaDesde = $value["fechaDesde"];
                $lineaspropuestas->fechaHasta = $value["fechaHasta"];
                if(isset($value["prefijo"]))
                    $lineaspropuestas->prefijo = $value["prefijo"];
                $lineaspropuestas->actividad = $value["actividad"];
                $lineaspropuestas->clasificacion = $value["clasificacion"];
                
                if (!$lineaspropuestas->save()) {
                    $errores .= "No se pudo guardar línea propuesta " . $propuesta->reg;
                }
            }
        }

        if ($req["listbarriospropuestas"] != null) {
            foreach ($req["listbarriospropuestas"] as $value) {
                $barriospropuestas = new BarriosPropuesta();
                $barriospropuestas->reg = $value["id"];
                $barriospropuestas->id_propuesta = $value["id_propuesta"];
                $barriospropuestas->id_barrio = $value["id_barrio"];
                $barriospropuestas->nombre = $value["nombre"];
                $barriospropuestas->ultmod = $value["ultmod"];
                $barriospropuestas->user_edit = $value["user_edit"];
                $barriospropuestas->codestado = $value["codestado"];
                $barriospropuestas->prefijo = $value["prefijo"];
                
                if (!$barriospropuestas->save()) {
                    $errores .= "No se pudo guardar línea propuesta " . $propuesta->reg;
                }
            }
        }

        if($req["rolpuntodeventa"] != "PRINCIPAL"){
            return $errores;
        }

        if ($req["listactividades"] != null) {
            foreach ($req["listactividades"] as $value) {
                $actividad = new Actividade();
                $cons = $data = DB::table('actividades')->where('cod',$value['cod'])->get();
                if(count($cons) > 0){
                    $actividad->where('cod',$value['cod'])->update([
                        "reg" => $value["id"],
                        "cod" => $value["cod"],
                        "nombre" => $value["nombre"],
                        "ultmod" => $value["ultmod"],
                        "user_edit" => $value["user_edit"],
                        "codestado" => $value["codestado"]
                    ]);
                }else{
                    $actividad->reg = $value["id"];
                    $actividad->cod = $value["cod"];
                    $actividad->nombre = $value["nombre"];
                    $actividad->ultmod = $value["ultmod"];
                    $actividad->user_edit = $value["user_edit"];
                    $actividad->codestado = $value["codestado"];
                    if (!$actividad->save()) {
                        $errores .= "No se pudo guardar la actividad " . $actividad->id;
                    }
                }
            }
        }


        if ($req["listclasificaciones"] != null) {
            foreach ($req["listclasificaciones"] as $value) {
                $clasificacion = new Clasificacione();
                $cons = $data = DB::table('clasificaciones')->where('reg',$value['id'])->get();
                if(count($cons) > 0){
                    $clasificacion->where('cod',$value['cod'])->update([
                        "reg" => $value["id"],
                        "cod" => $value["cod"],
                        "nombre" => $value["nombre"],
                        "id_actividad" => $value["id_actividad"],
                        "ultmod" => $value["ultmod"],
                        "user_edit" => $value["user_edit"],
                        "codestado" => $value["codestado"]
                    ]);
                }else{
                    $clasificacion->reg = $value["id"];
                    $clasificacion->cod = $value["cod"];
                    $clasificacion->nombre = $value["nombre"];
                    $clasificacion->id_actividad = $value["id_actividad"];
                    $clasificacion->ultmod = $value["ultmod"];
                    $clasificacion->user_edit = $value["user_edit"];
                    $clasificacion->codestado = $value["codestado"];
                    if (!$clasificacion->save()) {
                        $errores .= "No se pudo guardar línea clasificación " . $clasificacion->id;
                    }
                }
            }
        }

        if ($req["listcoberturas"] != null) {
            foreach ($req["listcoberturas"] as $value) {
                $cobertura = new Cobertura();
                $cons = $data = DB::table('coberturas')->where('nombre',$value['nombre'])->get();
                if(count($cons) > 0){
                    $cobertura->where('nombre',$value['nombre'])->update([
                        "suma" => $value["suma"],
                        "gastos" => $value["gastos"],
                        "deducible" => $value["deducible"],
                        "vrMensual" => $value["vrMensual"],
                        "vrTrimestral" => $value["vrTrimestral"],
                        "vrSemestral" => $value["vrSemestral"],
                        "x21" => $value["x21"],
                        "x32" => $value["x32"],
                        "x64" => $value["x64"],
                        "ultmod" => $value["ultmod"],
                        "user_edit" => $value["user_edit"],
                        "codestado" => $value["codestado"]
                    ]);
                }else{
                    $cobertura->id = $value["reg"];
                    $cobertura->nombre = $value["nombre"];
                    $cobertura->suma = $value["suma"];
                    $cobertura->gastos = $value["gastos"];
                    $cobertura->deducible = $value["deducible"];
                    $cobertura->vrMensual = $value["vrMensual"];
                    $cobertura->vrTrimestral = $value["vrTrimestral"];
                    $cobertura->vrSemestral = $value["vrSemestral"];
                    $cobertura->x21 = $value["x21"];
                    $cobertura->x32 = $value["x32"];
                    $cobertura->x64 = $value["x64"];
                    $cobertura->ultmod = $value["ultmod"];
                    $cobertura->user_edit = $value["user_edit"];
                    $cobertura->codestado = $value["codestado"];
                    if (!$cobertura->save()) {
                        $errores .= "No se pudo guardar línea cobertura " . $cobertura->reg;
                    }
                }
            }
        }




        return $errores;
    }
}
