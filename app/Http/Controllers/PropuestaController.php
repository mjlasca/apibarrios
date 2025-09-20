<?php



namespace App\Http\Controllers;

ini_set('memory_limit', '512M');

use App\Models\BarriosPropuesta;
use App\Models\cliente;
use App\Models\Cola;
use App\Models\LineasPropuesta;
use App\Models\logs;
use App\Models\migracionespunto;
use App\Models\Propuesta;
use App\Models\rendicione;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// SDK de Mercado Pago
use MercadoPago;

class PropuestaController extends Controller
{
    
    //
    public function __invoke()
    {
        //return response()->json( $get, 200);
    }

    public  function savepropuesta()
    {
        
        try
        {
            $request = request()->all();
            
            $datos["barriosagregados"] = json_decode( $request["barriosagregados"] );
            $datos["personasaseguradas"] = json_decode( $request["personasaseguradas"] );
            $datos["coberturavigen"] = json_decode( $request["coberturavigen"] );
            $datos["tomador"] = json_decode( $request["tomador"] );
            $datos["parametros"] = json_decode( $request["parametros"] );
        

            $propuesta = new Propuesta();
            $propuesta->prefijo = "O";
            $propuesta->documento = $datos["tomador"]->documento;
            
            $propuesta->nombre = $datos["tomador"]->nombres . " ".$datos["tomador"]->apellidos;
            $propuesta->num_polizas =  count($datos["personasaseguradas"]);
            $propuesta->meses = $datos["coberturavigen"]->meses;
            $propuesta->id_cobertura = $datos["coberturavigen"]->cobertura->nombre;
            $propuesta->fecha_nacimiento = $datos["tomador"]->fechanacimiento;
            $propuesta->nueva_poliza = $propuesta->num_polizas > 1 ? 1 : 2;
            $propuesta->premio = $datos["coberturavigen"]->premio;
            $propuesta->premio_total = $datos["coberturavigen"]->premiototal;
            $propuesta->fechaDesde = $datos["coberturavigen"]->vigenciadesde;
            $propuesta->fechaHasta = $datos["coberturavigen"]->vigenciahasta;
            $propuesta->clausula = count($datos["barriosagregados"]) > 0 ? 1 : 0;
            $propuesta->barrio_beneficiario = 0;
            $propuesta->ultmod = $datos["parametros"]->ultmod;
            $propuesta->useredit = "online";
            $propuesta->codestado = 0;
            $propuesta->cobertura_suma = $datos["coberturavigen"]->cobertura->suma;
            $propuesta->cobertura_deducible = $datos["coberturavigen"]->cobertura->deducible;
            $propuesta->cobertura_gastos = $datos["coberturavigen"]->cobertura->gastos;
            $propuesta->promocion = $datos["coberturavigen"]->promociones;
            $propuesta->paga = 0;
            $propuesta->fecha_paga = $datos["parametros"]->ultmod;
            $propuesta->referencia = null;
            $propuesta->prima = null;
            $propuesta->master = null;
            $propuesta->formadepago = null;
            $propuesta->organizador = null;
            $propuesta->productor = null;
            $propuesta->puntodeventa ="online";
            $propuesta->csrf = $datos["parametros"]->csrf;
            $propuesta->usuariopaga ="online";

            $cons = DB::table('propuestas')->where('csrf',$datos["parametros"]->csrf)->get();
            if(count($cons) > 0){
                $propuesta->reg = $cons[0]->reg;
                $propuesta->where('csrf',$datos["parametros"]->csrf)->update([

                    "prefijo" => "O",
                    "documento" => $datos["tomador"]->documento,
                    "nombre" => $datos["tomador"]->nombres . " ".$datos["tomador"]->apellidos,
                    "num_polizas" =>  count($datos["personasaseguradas"]),
                    "meses" => $datos["coberturavigen"]->meses,
                    "id_cobertura" => $datos["coberturavigen"]->cobertura->nombre,
                    "nueva_poliza" => $propuesta->num_polizas > 1 ? 1 : 2,
                    "premio" => $datos["coberturavigen"]->premio,
                    "premio_total" => $datos["coberturavigen"]->premiototal,
                    "fechaDesde" => $datos["coberturavigen"]->vigenciadesde,
                    "fechaHasta" => $datos["coberturavigen"]->vigenciahasta,
                    "clausula" => count($datos["barriosagregados"]) > 0 ? 1 : 0,
                    "barrio_beneficiario" => 0,
                    "ultmod" => $datos["parametros"]->ultmod,
                    "useredit" => "online",
                    "codestado" => 0,
                    "cobertura_suma" => $datos["coberturavigen"]->cobertura->suma,
                    "cobertura_deducible" => $datos["coberturavigen"]->cobertura->deducible,
                    "cobertura_gastos" => $datos["coberturavigen"]->cobertura->gastos,
                    "promocion" => $datos["coberturavigen"]->promociones,
                    "paga" => 0,
                    "fecha_paga" => $datos["parametros"]->ultmod,
                    "referencia" => null,
                    "prima" => null,
                    "master" => null,
                    "formadepago" => null,
                    "fecha_nacimiento" => $propuesta->fecha_nacimiento,
                    "organizador" => null,
                    "productor" => null,
                    "puntodeventa" =>"online",
                    "csrf" => $datos["parametros"]->csrf,
                    "usuariopaga" =>"online"

                ]);

            }else{
                $propuesta->reg = $propuesta->consecutivo();
                $propuesta->id_barrio = $propuesta->reg ;
                $propuesta->save();
            }

                $cliente  = new cliente();
                $cons = $cliente->where('id',$datos["tomador"]->documento)->get();
                if(count($cons) > 0){
                    $cliente->where('id',$datos["tomador"]->documento)->update([
                        "nombres" => $datos["tomador"]->nombres,
                        "apellidos" => $datos["tomador"]->apellidos,
                        "telefono" => $datos["tomador"]->telefono,
                        "direccion" => $datos["tomador"]->direccion,
                        "email" => $datos["tomador"]->email,
                        "ciudad" => $datos["tomador"]->ciudad,
                        "codpostal" => $datos["tomador"]->codpostal,
                        "localidad" => $datos["tomador"]->localidad,
                        "fecha_nacimiento" => $datos["tomador"]->fechanacimiento,
                        "tipo_id" => $datos["tomador"]->tipodocumento,
                        "sexo" => $datos["tomador"]->sexo,
                        "situacion" => $datos["tomador"]->situacionimpositiva,
                        "ultmod" => $datos["parametros"]->ultmod,
                        "user_edit" =>  "online",
                        "categoria" => "ONLINE"
                    ]);
                }else{
                    $cliente->id = $datos["tomador"]->documento;
                    $cliente->nombres = $datos["tomador"]->nombres;
                    $cliente->apellidos = $datos["tomador"]->apellidos;
                    $cliente->telefono = $datos["tomador"]->telefono;
                    $cliente->direccion = $datos["tomador"]->direccion;
                    $cliente->email = $datos["tomador"]->email;
                    $cliente->ciudad = $datos["tomador"]->ciudad;
                    $cliente->codpostal = $datos["tomador"]->codpostal;
                    $cliente->localidad = $datos["tomador"]->localidad;
                    $cliente->fecha_nacimiento = $datos["tomador"]->fechanacimiento;
                    $cliente->tipo_id = $datos["tomador"]->tipodocumento;
                    $cliente->sexo = $datos["tomador"]->sexo;
                    $cliente->situacion = $datos["tomador"]->situacionimpositiva;
                    $cliente->ultmod = $datos["parametros"]->ultmod;
                    $cliente->user_edit = "online";
                    $cliente->categoria = "ONLINE";
                    $cliente->save();
                }
                

                
                

                if (count($datos["personasaseguradas"])> 0) {
                    
                    DB::table('lineas_propuestas')->where('id_propuesta',$propuesta->reg)->where('prefijo',$propuesta->prefijo)->delete();

                    foreach ($datos["personasaseguradas"] as $value) {
                        $lineaspropuestas = new LineasPropuesta();
                        $lineaspropuestas->reg = $propuesta->reg ;
                        $lineaspropuestas->id_propuesta = $propuesta->reg ;
                        $lineaspropuestas->documento = $value->documento;
                        $lineaspropuestas->tipo_documento = $value->tipodocumento;
                        $lineaspropuestas->apellidos = $value->apellidos;
                        $lineaspropuestas->nombres = $value->nombres;
                        $lineaspropuestas->fecha_nacimiento = $value->fechanacimiento;
                        $lineaspropuestas->id_actividad = $value->actividad;
                        $lineaspropuestas->id_clasificacion = $value->clasificacion;
                        $lineaspropuestas->premio = $propuesta->premio;
                        $lineaspropuestas->ultmod = $propuesta->ultmod;
                        $lineaspropuestas->user_edit = $propuesta->useredit;
                        $lineaspropuestas->codestado = 0;
                        $lineaspropuestas->fechaDesde = $propuesta->fechaDesde;
                        $lineaspropuestas->fechaHasta = $propuesta->fechaHasta;
                        $lineaspropuestas->prefijo = $propuesta->prefijo;
                        $lineaspropuestas->actividad = $value->nomactividad;
                        $lineaspropuestas->clasificacion = $value->nomclasificacion;
                        
                        
                        $lineaspropuestas->save();
                    }
                }

        
                
                if ( count($datos["barriosagregados"]) > 0 ) {
                    
                    DB::table('barrios_propuestas')->where('id_propuesta',$propuesta->reg)->where('prefijo',$propuesta->prefijo)->delete();

                    foreach ($datos["barriosagregados"] as $value) {
                        $barriospropuestas = new BarriosPropuesta();
                        $barriospropuestas->reg = $propuesta->reg;
                        $barriospropuestas->id_propuesta = $propuesta->reg;
                        $barriospropuestas->id_barrio = $value->cuit;
                        $barriospropuestas->nombre = $value->nombre;
                        $barriospropuestas->ultmod = $propuesta->ultmod;
                        $barriospropuestas->user_edit = $propuesta->useredit;
                        $barriospropuestas->codestado = 1;
                        $barriospropuestas->prefijo = $propuesta->prefijo;
                        $barriospropuestas->save(); 
                        
                    }
                }

                // Agrega credenciales
                MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));
                // Crea un objeto de preferencia
                $preference = new MercadoPago\Preference();

                // Crea un ítem en la preferencia
                $item = new MercadoPago\Item();
                $item->title = "Póliza No. ".$propuesta->prefijo."-".$propuesta->reg." | "."Tomador : ".$propuesta->nombre;
                $item->quantity = 1;
                $item->unit_price = $propuesta->premio_total;

                $preference->back_urls = array(
                    "success" => url('/polizas')."?estado=success&idpropuesta=".$propuesta->reg."&prefijo=".$propuesta->prefijo,
                    "failure" => url('/cotizadoronline')."?estado=failure",
                    "pending" => url('/polizas')."?estado=pending&idpropuesta=".$propuesta->reg."&prefijo=".$propuesta->prefijo
                );

                $preference->items = array($item);
                $preference->save();
                
                //total=590&idpropuesta=2&prefijo=O&tomador=MARIO%20LASLUISA%20CASTAÑO
            
                return response()->json(
                    [
                        'res' => 'Se ha generado la propuesta con éxito', 'success'=>true, 
                        'idpropuesta' => $propuesta->reg,
                        'prefijo' => $propuesta->prefijo,
                        'total' => $propuesta->premio_total,
                        'tomador' => $propuesta->nombre,
                        'preference' => $preference->id

                    ], 202);

        }catch(Exception $ex){
            $logs = new logs();
            $logs->saveerror($ex->getMessage(), $propuesta->reg, $propuesta->prefijo, "101");
            return response()->json(['res' => 'No se ha podido guardar la propuesta error #101', 'success'=>false], 400);
        }

        
    }


    public function paypropuesta()
    {
        $data = request()->all();
        return view('pay.index', compact('data'));
    }


    public function descargapdfpoliza()
    {
        $data = request()->all();

        $data = DB::table('propuestas')->where('idpropuesta',$data['id'])->where('prefijo', $data['prefijo'])->get();

        if (count($data) > 0) {

            $lineasdata = DB::table('lineas_propuestas')->where('id_propuesta',$data[0]->idpropuesta)->where('prefijo',$data[0]->prefijo)->where('codempresa',$data[0]->codempresa)->groupBy('documento')->get();
            if(isset($data[0]->data_barrios) && $data[0]->data_barrios != ""){
                $barriospropuesta = json_decode( $data[0]->data_barrios);
                $barriospropuesta = $barriospropuesta->barrios;
            }
            else
                $barriospropuesta = DB::table('barrios_propuestas')->where('id_propuesta',$data[0]->reg)->where('prefijo',$data[0]->prefijo)->where('codempresa',$data[0]->codempresa)->get();

            $cliente = DB::table('clientes')->where('id',$data[0]->documento)->get();
        
            $pdf = PDF::loadView('pdf-propuesta.index', compact('cliente','data','lineasdata','barriospropuesta'));

            return $pdf->stream();
        } else {
            return response()->json(['res' => 'El documento solicitado no existe o no se ha generado en la nube'], 400);
        }
    }


    public function cotizadoronline()
    {
        $data["coberturas"] = DB::table('coberturas')->where('codestado','=', '1')->get();
        $data["actividades"] = DB::table('actividades')->where('codestado','=', '1')->get();
        $data["clasificaciones"] = DB::table('clasificaciones')->where('codestado','=', '1')->get();
        $data["provincias"] = DB::table('provincias')->where('codestado','=', '1')->get();
        $data["barrios"] = DB::table('barrios')->where('codestado','=', '1')->get();
        $data["gruposbarrios"] = DB::table('gruposbarrios')->get();
        $data["gruposbarriosnombres"] = DB::table('gruposbarrios')->select('nombre','id')->distinct()->get();

        /*MercadoPago\SDK::setAccessToken('PROD_ACCESS_TOKEN');
        $preference = new MercadoPago\Preference();
        // Create a preference item
        $item = new MercadoPago\Item();
        $item->title = 'My Item';
        $item->quantity = 1;
        $item->unit_price = 75;
        $preference->items = array($item);
        $preference->save();*/

        return view('cotizadoronline.index', compact('data'));
        
    }

    public function paypro(){

        $req = request()->all();

        if(!empty($req)){
            $propExist = Propuesta::where('idpropuesta',$req["idpropuesta"])->where('prefijo',$req["prefijopropuesta"])->first();
            if(!empty($propExist)){
                $prop = new Propuesta();
                if($prop->pagarpropuesta(
                    $req["idpropuesta"],
                    $req["prefijopropuesta"],
                    $req["tipopago"],
                    $req["compformapago"],
                    $req["usuariopaga"],
                    $req["fecha_paga"],
                    $req["codempresa"],
                    $req["version"],
                    $req["fecha_comprobante"],
                    $req["valor_pagado"]
                ))
                    return response()->json(['res' => 'Se ha hecho el pago de la propuesta con éxito'], 200);
            }else{
                return response()->json(['res' => 'La propuesta no existe'], 404 );    
            }
            
            return response()->json(['res' => 'No se pudo hacer el pago de la propuesta'], 400);
            
        }
        
    }


    public function polizas()
    {
        $data = [];
        $success = true;
        $estado = "";
        $req = request()->all();
        if($req){
            $prop = new Propuesta();
            $estado = request()->get('estado');
        }
        return view('polizas.index', compact('data','success','estado'));
    }

    

    public function consultapoliza()
    {
        $data = request()->all();
        $estado = "";


        $sql = "SELECT lp.* 
                FROM propuestas p 
                INNER JOIN lineas_propuestas lp ON p.prefijo = lp.prefijo AND p.reg = lp.id_propuesta  AND (p.codempresa = lp.codempresa OR p.codempresa IS NULL)
                WHERE 
                ((lp.fechaHasta >= '".date("Y-m-d H:i:s")."' AND lp.fecha_nacimiento = '".$data['fechanacimiento']."' AND lp.documento = '".$data['documento']."')
                 OR
                (p.fechaHasta >= '".date("Y-m-d H:i:s")."' AND p.fecha_nacimiento = '".$data['fechanacimiento']."' AND p.documento = '".$data['documento']."'))
                AND p.codestado > 0 AND p.paga > 0
                GROUP BY lp.id_propuesta
                ";
        $data = DB::select($sql);

        $success = false;        

        if (count($data) > 0) {
            $success = true;
            // Almacenar una variable en la sesión
            session(['get_prop' => true]);

            return view('polizas.index', compact('data','success','estado'));
        } else {
            return view('polizas.index', compact('data','success','estado'));
        }
    }

    

    public function consultaparametros()
    {
        try{
            $req = request()->all();
            $datos = [];

            if(isset($req['cola']) && $req['cola'] != ''){
                if(isset($req["apiversion"])){
                    if($req["apiversion"] == "3"){
                        if(isset($req["solicitud"])){
                            if(!empty($req['reset']) && $req['reset']== 1){
                                $colas = Cola::where('id', '>', $req['cola'])
                                    ->where('entity', str_replace('solicitud_','',$req['solicitud']))
                                    ->where(function ($query) use ($req) {
                                        $query->where('codempresa', $req['codempresa'])
                                              ->orWhere('codempresa', 'all');
                                    })
                                    ->where('updated_at', '>=', Carbon::now()->subDays(30))
                                    ->groupBy('entity', 'entity_id')
                                    ->orderBy('id','DESC')
                                    ->get(['entity', 'entity_id'])
                                    ->groupBy('entity')
                                    ->map(function ($group) {
                                        return $group->pluck('entity_id')->toArray();
                                    })
                                    ->toArray();
                                if(empty($colas)){
                                    $colas = [str_replace('solicitud_','',$req['solicitud']) => $req['solicitud']];
                                }
                            }else{
                                $colas = Cola::where('id', '>', $req['cola'])
                                    ->where(function($query) use ($req) {
                                        if($req['solicitud'] == 'solicitud_propuestas'){
                                            $query->where('ptoventa', '!=', $req["prefijositio"])->orWhereNull('ptoventa');
                                        }else{
                                            $query->where('ptoventa','!=',$req["prefijositio"]);
                                        }
                                    })
                                    ->where('entity', str_replace('solicitud_','',$req['solicitud']))
                                    ->where(function ($query) use ($req) {
                                        $query->where('codempresa', $req['codempresa'])
                                              ->orWhere('codempresa', 'all');
                                    })
                                    ->groupBy('entity', 'entity_id')
                                    ->orderBy('id','DESC')
                                    ->limit(30)
                                    ->get(['entity', 'entity_id']) 
                                    ->groupBy('entity')
                                    ->map(function ($group) {
                                        return $group->pluck('entity_id')->toArray();
                                    })
                                    ->toArray();
                            }
                            if(!empty($colas)){
                                if($req["solicitud"] == "solicitud_propuestas" && !empty($colas['propuestas'])){
                                    $sql = "SELECT t1.* FROM propuestas t1 
                                    WHERE t1.codempresa = '".$req["codempresa"]."' AND t1.id IN (".implode(',',$colas['propuestas']).")  ORDER BY t1.ultmod DESC;";
                                    $datos["propuestas"] = DB::select($sql);
                                    $idsPropuesta = array_map(function($pro) {
                                        return $pro->id;
                                    }, $datos["propuestas"]);
                                    $idsClientes = array_map(function($pro) {
                                        return $pro->documento;
                                    }, $datos["propuestas"]);
                                    //líneas propuestas
                                    $sql = "SELECT t1.* FROM lineas_propuestas t1 INNER JOIN propuestas t2 ON t1.prefijo = t2.prefijo AND t1.id_propuesta = t2.idpropuesta WHERE t2.id IN (".implode(',',$idsPropuesta).")  AND ( t2.prefijo != '".$req["prefijositio"]."' OR (t2.usuariopaga != '' AND t2.prefijo = '".$req["prefijositio"]."'))";
                                    
                                    $datos["lineas_propuestas"] = DB::select($sql);

                                    $idsClientes_ = array_map(function($lin) {
                                        return $lin->documento;
                                    }, $datos["lineas_propuestas"]);
                                    if(empty($req['reset']))
                                        $datos["clientes"] = DB::table('clientes')->whereIn('id',array_unique(array_merge($idsClientes,$idsClientes_)))->groupBy('id')->get();
                                }
                                
                                if($req["solicitud"] == "solicitud_barrios" && !empty($colas['barrios'])){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["barrios"] = DB::table('barrios')->get();
                                    else
                                        $datos["barrios"] = DB::table('barrios')->whereIn('reg',$colas['barrios'])->get();
                                }
                                
                                if($req["solicitud"] == "solicitud_clientes" && !empty($colas['clientes']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["clientes"] = DB::table('clientes')->where('codempresa',$req['codempresa'])->groupBy('id')->get();
                                    else
                                        $datos["clientes"] = DB::table('clientes')->whereIn('reg',$colas['clientes'])->groupBy('id')->get();
                                }
        
                                if($req["solicitud"] == "solicitud_usuarios"  && !empty($colas['usuarios']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["usuarios"] = DB::table('usuarios')->where('codempresa',$req['codempresa'])->get();
                                    else
                                        $datos["usuarios"] = DB::table('usuarios')->whereIn('reg',$colas['usuarios'])->get();
                                }
                                if($req["solicitud"] == "solicitud_perfiles"  && !empty($colas['perfiles']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["perfiles"] = DB::table('perfiles')->where('codempresa',$req['codempresa'])->get();
                                    else
                                        $datos["perfiles"] = DB::table('perfiles')->whereIn('reg',$colas['perfiles'])->get();
                                }
                                
                                
                                if($req["solicitud"] == "solicitud_arqueos" && !empty($colas['arqueos']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["arqueos"] = DB::table('arqueos')->where('codempresa',$req['codempresa'])->limit(30)->where('puntodeventa','!=',$req["prefijositio"])->get();
                                    else
                                        $datos["arqueos"] = DB::table('arqueos')->whereIn('reg',$colas['arqueos'])->where('puntodeventa','!=',$req["prefijositio"])->get();
                                }
                                
    
                                if($req["solicitud"] == "solicitud_rendiciones" && !empty($colas['rendiciones']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1){
                                        $datos["rendiciones"] = rendicione::where('codempresa',$req['codempresa'])->orderBy('id','DESC')->limit(30)->get();
                                        $groupLineas = $datos["rendiciones"]->pluck('id')->toArray();
                                        if(!empty($groupLineas)){
                                            $sql = "SELECT t1.* FROM lineas_rendiciones t1 INNER JOIN rendiciones t2 ON t1.idrendicion = t2.reg 
                                            WHERE t2.id IN (".implode(',',$groupLineas).") ";
                                            $datos["lineas_rendiciones"] = DB::select($sql);
                                        }
                                        
                                    }
                                    else{
                                        $datos["rendiciones"] = DB::table('rendiciones')->whereIn('reg',$colas['rendiciones'])->where('puntodeventa','!=',$req["prefijositio"])->get();
                                        $sql = "SELECT t1.* FROM lineas_rendiciones t1 INNER JOIN rendiciones t2 ON t1.idrendicion = t2.reg 
                                        WHERE t2.id IN (".implode(',',$colas['rendiciones']).") ";
                                        $datos["lineas_rendiciones"] = DB::select($sql);
                                    }
                                    
                                }
        
                            
                                if($req["solicitud"] == "solicitud_actividades" && !empty($colas['actividades']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["actividades"] = DB::table('actividades')->limit(2)->get();
                                    else
                                        $datos["actividades"] = DB::table('actividades')->whereIn('reg',$colas['actividades'])->get();
                                }
    
                                
                                if($req["solicitud"] == "solicitud_coberturas" && !empty($colas['coberturas']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["coberturas"] = DB::table('coberturas')->limit(2)->get();
                                    else
                                        $datos["coberturas"] = DB::table('coberturas')->whereIn('reg',$colas['coberturas'])->get();
                                }
    
                                if($req["solicitud"] == "solicitud_clasificaciones" && !empty($colas['clasificaciones']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["clasificaciones"] = DB::table('clasificaciones')->limit(2)->get();
                                    else
                                        $datos["clasificaciones"] = DB::table('clasificaciones')->whereIn('reg',$colas['clasificaciones'])->get();
                                }
    
                                if($req["solicitud"] == "solicitud_gruposbarrios" && !empty($colas['gruposbarrios']) ){
                                    if( !empty($req['reset']) && $req['reset'] == 1)
                                        $datos["gruposbarrios"] = DB::table('gruposbarrios')->get();
                                    else
                                        $datos["gruposbarrios"] = DB::table('gruposbarrios')->whereIn('reg',$colas['gruposbarrios'])->get();
                                }
                                if($req["solicitud"] == "solicitud_provincias"){
                                    $datos["provincias"] = DB::table('provincias')->get();  
                                }
                                if(!empty($req['reset']) && $req['reset']== 1){
                                    $colas = Cola::where('id', '>', $req['cola'])
                                    ->where('entity', str_replace('solicitud_','',$req['solicitud']))
                                    ->where(function ($query) use ($req) {
                                        $query->where('codempresa', $req['codempresa'])
                                              ->orWhere('codempresa', 'all');
                                    })
                                    ->where('updated_at', '>=', Carbon::now()->subDays(30))
                                    ->groupBy('entity', 'entity_id')
                                    ->orderBy('id','DESC')
                                    ->select('entity', 'entity_id','id') 
                                    ->groupBy('entity')
                                    ->first();
                                    if(empty($colas)){
                                        $colas = [
                                            "entity" => str_replace('solicitud_','',$req['solicitud']),
                                            "entity_id" => 0,
                                            "id" => 0
                                        ];
                                    }
                                    $datos['colas'] = [$colas];
                                }else{
                                    $colas = Cola::where('id', '>', $req['cola'])
                                    ->where(function($query) use ($req) {
                                        if($req['solicitud'] == 'solicitud_propuestas'){
                                            $query->where('ptoventa', '!=', $req["prefijositio"])->orWhereNull('ptoventa');
                                        }else{
                                            $query->where('ptoventa','!=',$req["prefijositio"]);
                                        }
                                    })
                                    ->where('entity', str_replace('solicitud_','',$req['solicitud']))
                                    ->where(function ($query) use ($req) {
                                        $query->where('codempresa', $req['codempresa'])
                                              ->orWhere('codempresa', 'all');
                                    })
                                    ->groupBy('entity', 'id')
                                    ->orderBy('id','DESC')
                                    ->limit(30)
                                    ->select('entity', 'entity_id','id') 
                                    ->groupBy('entity','id')
                                    ->get();
                                    $datos['colas'] = $colas;
                                    if(!empty($datos['colas'])){
                                        $maxCola = Cola::where('entity', str_replace('solicitud_','', $req['solicitud']))->max('id');
                                        if(($maxCola - $colas->max('id')) > 100){
                                            logs::newMsg(
                                                "{$req['solicitud']} último dato cola {$colas->max('id')} - {$maxCola}",
                                                "COLA-{$req['prefijositio']}",
                                                $req['solicitud'],
                                                $req['prefijositio']
                                            );    
                                            $datos['colas'][] = [
                                                "entity" => str_replace('solicitud_','', $req['solicitud']),
                                                "entity_id" => '',
                                                "id" => ($maxCola - 100)
                                            ];
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                }
            }
            
            return response()->json($datos, 200);

            
        }catch (Exception $ex){
            $jsonData = json_encode($req);
            $logs = new logs();
            $logs->saveerror("Error en importarción ".$ex->getMessage()."/n".$jsonData,"", "", "IMP101");
            return response()->json("Error en importarción ".$ex->getMessage(), 404);

        }
        
        
    }

}
