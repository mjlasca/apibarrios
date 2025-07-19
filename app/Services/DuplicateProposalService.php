<?php
namespace App\Services;

use App\Models\LineasPropuesta;
use App\Models\Propuesta;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Validator;

class DuplicateProposalService
{
    public function duplicate($newPref = 'O', $data = []) : mixed {

        $prop = Propuesta::where('prefijo', $data['pref'])->where('idpropuesta', $data['id'])->first();

        if(  $prop ){

            $propNew = new Propuesta;
            $propNew->prefijo = $newPref;
            $propNew->idpropuesta = $prop->consecutive($newPref);
            $propNew->reg = $propNew->idpropuesta;
            $propNew->documento = $prop->documento;
            $propNew->nombre = $prop->nombre;
            $propNew->num_polizas = $prop->num_polizas;
            $propNew->meses = isset($data['meses']) ? $data['meses'] : $prop->meses;
            $propNew->id_cobertura = $prop->id_cobertura;
            $propNew->id_barrio = $prop->id_barrio;
            $propNew->nueva_poliza = $prop->nueva_poliza;
            $propNew->premio =  isset($data['premio']) ? $data['premio'] : $prop->premio;
            $propNew->premio_total = isset($data['premio_total']) ? $data['premio_total'] : $prop->premio_total;

            $fecha = new DateTime(now('America/Argentina/Buenos_Aires'));
            if(isset($data["fecha_desde"])){
                $fecDesde = new DateTime( $data['fecha_desde']);
                $propNew->fechaDesde = $fecDesde->format('Y-m-d 00:00:00');
                $propNew->fechaHasta = $fecDesde->modify("+".$data['meses']." months")->format('Y-m-d 23:59:00');
            }
                
            $propNew->clausula = $prop->clausula;
            $propNew->barrio_beneficiario = $prop->barrio_beneficiario;
            $fecha_1 = new DateTime(now('America/Argentina/Buenos_Aires'));
            $dayName = $fecha_1->format('1');

            if($dayName == 'Saturday')
                $fecha_1 = $fecha_1->modify("+2 day")->format('Y-m-d 08:00:00');
            else if($dayName == 'Sunday')
                $fecha_1 = $fecha_1->modify("+1 day")->format('Y-m-d 08:00:00');
            else if ($fecha_1->format('H:i') >= '16:00') {
                if($dayName == 'Friday')
                    $fecha_1 = $fecha_1->modify("+3 day")->format('Y-m-d 08:00:00');
                else
                    $fecha_1 = $fecha_1->modify("+1 day")->format('Y-m-d 08:00:00');
            }
            else
                $fecha_1 = $fecha_1->format('Y-m-d H:i:s');

            $propNew->ultmod = $fecha_1;
            $propNew->useredit = 'online';
            $propNew->codestado = '1';
            $propNew->cobertura_suma = $prop->cobertura_suma;
            $propNew->cobertura_deducible = $prop->cobertura_deducible;
            $propNew->cobertura_gastos = $prop->cobertura_gastos;
            $propNew->promocion = $prop->promocion;
            $propNew->paga = 0;
            $propNew->formadepago = 'CREDITO';
            $propNew->usuariopaga = 'online';
            if(isset($data['forma_pago']))
                $propNew->tipopago = $data['forma_pago'] ;
            if(isset($data['nro_comprobante']))
                $propNew->compformadepago = $data['nro_comprobante'];
            $propNew->fecha_nacimiento = $prop->fecha_nacimiento;
            $propNew->codempresa = $prop->codempresa;
            $propNew->data_barrios = $prop->data_barrios;
            $propNew->master = $prop->master;
            $propNew->organizador = $prop->organizador;
            $propNew->productor = $prop->productor;

            if($propNew->save()){

                $lines = LineasPropuesta::where('prefijo', $data['pref'])->where('id_propuesta', $data['id'])->get();
                foreach ($lines as $key => $value) {
                    $line = new LineasPropuesta();
                    $line->reg = $value->reg;
                    $line->id_propuesta = $propNew->idpropuesta;
                    $line->documento = $value->documento;
                    $line->tipo_documento = $value->tipo_documento;
                    $line->apellidos = $value->apellidos;
                    $line->nombres = $value->nombres;
                    $line->fecha_nacimiento = $value->fecha_nacimiento;
                    $line->id_actividad = $value->id_actividad;
                    $line->id_clasificacion = $value->id_clasificacion;
                    $line->premio = $propNew->premio;
                    $line->ultmod = $propNew->ultmod;
                    $line->user_edit = $propNew->useredit;
                    $line->codestado = 1;
                    $line->prefijo = $propNew->prefijo;
                    $line->actividad = $value->actividad;
                    $line->clasificacion = $value->clasificacion;
                    $line->clasificacion = $value->clasificacion;
                    $line->fechaDesde = $propNew->fechaDesde;
                    $line->fechaHasta = $propNew->fechaHasta;
                    $line->codempresa = $value->codempresa;
                    $line->save();
                }


            }
            return $propNew;
        }
        return FALSE;
    }
}
