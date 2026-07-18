<?php

namespace App\Models;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\logs;
use App\Observers\ProposalObserver;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Propuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg',
        'codempresa',
        'prefijo',
        'idpropuesta',
        'codestado',
        'documento',
        'nombre',
        'num_polizas',
        'meses',
        'clausula',
        'id_cobertura',
        'id_barrio',
        'nueva_poliza',
        'premio',
        'premio_total',
        'fechaDesde',
        'fechaHasta',
        'ultmod',
        'useredit',
        'cobertura_suma',
        'cobertura_deducible',
        'cobertura_gastos',
        'promocion',
        'paga',
        'fecha_paga',
        'master',
        'organizador',
        'productor',
        'data_barrios',
        'version',
        'fecha_comprobante',
        'fecha_nacimiento',
        'formadepago',
    ];

    public function consecutivo(){
        
        $cons = DB::table('propuestas')->where('prefijo','O')->orderBy('idpropuesta','DESC')->first();
        if($cons)
            return $cons->idpropuesta + 1;
        else
            return 1;
    }

    public function pagarpropuesta($idpropuesta, $prefijo,$formadepago,$idpago,$userpay = "online",$fecha_paga = "", $codempresa = "default", $version = 0, $fecha_comprobante = null, $valor_pagado = 0, $cuit_pagador = ""){

        try{
            $propuesta = new Propuesta();
            $pay_ = payregistry::where('idpropuesta',$idpropuesta)->where('prefijo',$prefijo)->first();
            if(empty($pay_)){
                if($fecha_paga == "")   
                $fecha_paga = date("Y-m-d H:i:s");
                
                $propuesta = $propuesta->where('idpropuesta',$idpropuesta)->where('prefijo',$prefijo)->where('codempresa',$codempresa)->update([
                        "codestado" => 1,
                        "paga" => 1,
                        "csrf" => null,
                        "usuariopaga" => $userpay,
                        "fecha_paga" => $fecha_paga,
                        "tipopago" => $formadepago,
                        "compformadepago" => $idpago,
                        "version" => $version + 1,
                        "fecha_comprobante" => $fecha_comprobante,
                        "valor_pagado" => $valor_pagado,
                        "cuit_pagador" => $cuit_pagador
                    ]);
                $propuesta = Propuesta::where('idpropuesta',$idpropuesta)->where('prefijo',$prefijo)->first();
                    
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
                $pay->fecha_comprobante = $fecha_comprobante;
                $pay->valor_pagado = $valor_pagado;
                $pay->cuit_pagador = $cuit_pagador;
                $pay->save();
                
                Cola::create([
                    'entity' => 'propuestas',
                    'entity_id' => $propuesta->id,
                    'codempresa' => $propuesta->codempresa,
                ]);

                return true;
            }else{

                return false;
            }

        }catch(Exception $ex){
            $logs = new logs();
            $logs->saveerror($ex->getMessage(), "", "", "150");
            
            return false;
        }
    }


    public function validateProposal($req){

        $data = [];

            if (isset($req['ref']) && $req['ref'] != "" && strpos($req['ref'],'-') > -1) {
    
                $partes = explode('-', $req['ref']);
                $req['pref'] = strtoupper($partes[0]);
                $req['id'] = $partes[1];
            }

            if((isset($req['pref']) && isset( $req['id']) ) && ($req['pref'] != '' && $req['id'] != '')){
                $prefijo = $req['pref'];
                $idPropuesta = $req['id'];
                $data = Propuesta::query()
                ->where(function ($query) use ($prefijo, $idPropuesta) {
                    $query->where('prefijo', $prefijo)
                        ->where('idpropuesta', $idPropuesta);
                })->get();    
            }

            if(count($data) > 0)
                return $data;
    
            if(isset($req['ref']) && $req['ref'] != "" ){
                $referencia = $req['ref'];
                $data = Propuesta::query()
                        ->where(function ($query) use ($referencia) {
                            $query->where('referencia', $referencia);
                        })->get();
            }

        return $data;
    }

    public function calculateProposedTotal($pref, $id, $monthly){
        $data = [];

            $prop = Propuesta::where('prefijo', $pref)->where('idpropuesta', $id)->get();
            $concatInfo = "";
            if(count($prop)>0){
                
                $prop_lines = LineasPropuesta::where('prefijo',$pref)->where('id_propuesta', $id)->get();
                
                if(count($prop_lines) > 0){
                    
                    
                    $cobertura = Cobertura::query()->where('nombre',$prop[0]->id_cobertura)->get();
                    $total = 0;
                    $total_prom = $cobertura[0]->vrMensual * $monthly;

                    if($monthly == 2){
                        if($cobertura[0]->x21 != '' && $cobertura[0]->x21 > 0){
                            $total_prom = $cobertura[0]->x21;
                        }
                            
                            
                    }

                    if($monthly == 3){
                        if($cobertura[0]->vrTrimestral != '' && $cobertura[0]->vrTrimestral > 0)
                            $total_prom = $cobertura[0]->vrTrimestral;
                        if($cobertura[0]->x32 != '' && $cobertura[0]->x32 > 0)
                            $total_prom = $cobertura[0]->x32;
                    } 

                    if($monthly == 6){
                        if($cobertura[0]->vrSemestral != '' && $cobertura[0]->vrSemestral > 0)
                            $total_prom = $cobertura[0]->vrSemestral;
                        if($cobertura[0]->x64 != '' && $cobertura[0]->x64 > 0)
                            $total_prom = $cobertura[0]->x64;
                    } 

                    
                    if(count($cobertura) > 0){

                        $total =  $total_prom * count($prop_lines);
                        foreach ($prop_lines as $key => $value) {

                            /*if( $this->calculateAge( $value->fecha_nacimiento ) >= 65){
                                $total += $total_prom;
                            }*/
                            $concatInfo .= $value->apellidos. " ". $value->nombres . " ". $value->tipo_documento. ":".$value->documento. " ".$value->fecha_nacimiento. " ";
                        }
                        $data['vrunit'] = $cobertura[0]->vrMensual;
                        $data['total'] = $total;
                        $data['info'] = $concatInfo;
                        $data['pref'] = $prop[0]->prefijo;
                        $data['id'] = $prop[0]->idpropuesta;
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                        $domain = $_SERVER['HTTP_HOST'];
                        //$url = $protocol . $domain;
                        //$data['url'] = $url.'/propuesta-duplicate/descargaseguro/'.$id.'/'.$pref.'/;
                        

                        return $data;
                    }
                    
                    
                }
            }


        return $data;
    }


    public function duplicate($newPref = 'O', $data = []){

        $prop = Propuesta::where('prefijo', $data['pref'])->where('idpropuesta', $data['id'])->first();

        if(  $prop ){

            $propNew = new Propuesta;
            $propNew->prefijo = $newPref;
            $propNew->idpropuesta = $this->consecutive($newPref);
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
            
            $fecha_hasta_a = new DateTime( $prop->fechaHasta );
            
            if($fecha < $fecha_hasta_a){
                $fecha = $fecha_hasta_a->modify("+1 day");
            }

            $propNew->fechaDesde = $fecha->format('Y-m-d H:i:s');                
            $propNew->fechaHasta =  isset($data['meses']) ? $fecha->modify("+".$data['meses']." months")->format('Y-m-d H:i:s') : $fecha->modify("+1 months")->format('Y-m-d H:i:s');
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
            $propNew->paga = 1;
            $propNew->fecha_paga = $propNew->ultmod;
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

    public function calculateAge($date) {
        $currentDate = date('Y-m-d');
        list($year, $mont, $day) = explode("-", $date);
        list($currentYear, $currentMont, $currentDay) = explode("-", $currentDate);
    
        $age = $currentYear - $year;
    
        if ($currentMont < $mont || ($currentMont == $mont && $currentDay < $day)) {
            $age--;
        }
    
        return $age;
    }

    public function consecutive($pref){
        $maxid = Propuesta::where('prefijo', $pref)->max('idpropuesta');

        $newid = 1;
        if ($maxid) {
            $newid = $maxid + 1;
        } 

        return $newid;
    }
    
    public function cliente(){
        return $this->belongsTo(cliente::class);
    }
    
}
