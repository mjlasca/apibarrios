<?php

namespace App\Http\Controllers;

use App\Models\barrio;
use App\Models\cliente;
use App\Models\Cola;
use App\Models\gruposbarrio;
use App\Models\LineasPropuesta;
use App\Models\logs;
use App\Models\payregistry;
use App\Models\PendingDuplicate;
use App\Models\Propuesta;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




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
                $res = Propuesta::where('idpropuesta', $registro['idpropuesta'])
                            ->where('prefijo', $registro['prefijo'])
                            ->where('codempresa', $codempresa)
                            ->update([
                                'referencia' => $registro['referencia'],
                                'nota' => $registro['nota'],
                                'prima' => $registro['prima'],
                                'version' => DB::raw('version + 1'),
                            ]);
                if($res > 0){
                    $propuesta = Propuesta::where('idpropuesta',$registro['idpropuesta'])->where('prefijo',$registro['prefijo'])->first();
                    Cola::create([
                        'entity' => 'propuestas',
                        'entity_id' => $propuesta->id,
                        'codempresa' => $propuesta->codempresa,
                    ]);
                }
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

            $regpending = PendingDuplicate::where('prefijo', strtoupper($req['pref']))->where('idpropuesta', $req['id'])->where('status',0)->first();
            
                if( $regpending ){
                    
                    $prop = new Propuesta();
                    
                    $data= [
                        'meses' => $regpending->meses,
                        'premio' => $regpending->premio,
                        'premio_total' => $regpending->premio_total,
                        'pref' => strtoupper($req['pref']),
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

    public function descargarPdfLibreDeuda($id,$prefijo){
        if(!empty($id) && !empty($prefijo)){
            $data = DB::table('propuestas')->where('idpropuesta',$id)->where('prefijo', $prefijo)->get();

            if (count($data) > 0) {

                $lineasdata = DB::table('lineas_propuestas')->where('id_propuesta',$data[0]->idpropuesta)->where('prefijo',$data[0]->prefijo)->where('codempresa',$data[0]->codempresa)->groupBy('documento')->get();
                if(isset($data[0]->data_barrios) && $data[0]->data_barrios != ""){
                    $barriospropuesta = json_decode( $data[0]->data_barrios);
                    $barriospropuesta = $barriospropuesta->barrios;
                }
                else
                    $barriospropuesta = DB::table('barrios_propuestas')->where('id_propuesta',$data[0]->reg)->where('prefijo',$data[0]->prefijo)->where('codempresa',$data[0]->codempresa)->get();

            
                $pdf = PDF::loadView('pdf-libre-deuda.index', compact('data','lineasdata','barriospropuesta'));

                return $pdf->stream();
            }
        }

        return response()->json(['res' => 'El documento solicitado no existe o no se ha generado'], 404);
        
    }

    public function agregar_barrios(Request $request, gruposbarrio $gruposbarrios, $prefijo, $idpropuesta){
        
        $propuesta = Propuesta::where('prefijo', $prefijo)->where('idpropuesta',$idpropuesta)->get();

        if(count($propuesta) > 0){
            
            $exclude = json_decode( $propuesta[0]->data_barrios );
            $excludedIdBarrios = collect($exclude->barrios)->pluck('id_barrio')->toArray();

            
            $arr_group = session('arr_group');
            if(empty($arr_group))
                $arr_group = [];
            //dump($excludedIdBarrios);
            $valorComparacion = $propuesta[0]->cobertura_suma;
            $barrios_ = barrio::select('id') 
                ->whereNotNull('suma_muerte')
                ->where('suma_muerte', '<=', $valorComparacion)
                ->whereNotIn('id', $excludedIdBarrios)
                ->get();

            //dump($barrios_);
            $barr = [];
            foreach ($barrios_ as $key => $value) {
                $barr[] = $value->id;
            }
            
            //dump( implode(",", $barr ));
            $grupos = GruposBarrio::
            whereIn('idbarrio', $barr)
            ->groupBy('id')
            ->orderBy('nombre','asc')
            ->whereNotIn('id',$arr_group)
            ->get();
            
            $barrios = barrio::where('nombre', 'LIKE', "%$request->search%")->orWhere('id',$request->search)->where('suma_muerte', '>=', $valorComparacion)->whereNotIn('id', $excludedIdBarrios)->orderBy('nombre','asc')->latest()->paginate();
            
            return view('propuestas.agregar-barrios', ['gruposbarrios' => $grupos, 'propuesta' => $propuesta[0], 'barrios' => $barrios]);  
        }

        return view('propuestas.agregar-barrios', ['gruposbarrios' => [], 'propuesta' => $propuesta = new Propuesta, 'barrios' => $barrios = [] ]);  

        
    }

    public function agregar_barrios_barrio(Request $request){

        $request->validate([
            'id' => 'required',
            'prefijo' => 'required'
        ]);

        try{
            $data_barrios = Propuesta::where('prefijo', $request->prefijo)->where('idpropuesta',$request->id)->value('data_barrios');
            $suma = Propuesta::where('prefijo', $request->prefijo)->where('idpropuesta',$request->id)->value('cobertura_suma');

            if($data_barrios){

                $savetrue = false;
                $data_barrios =  json_decode($data_barrios);
                $coleccionBarrios = collect($data_barrios->barrios);

                if(isset($request->grupo)){

                    $grupoBarrios = gruposbarrio::where('id',$request->grupo)->groupBy('idbarrio')->get();
//dump($grupoBarrios);                    
                    foreach ($grupoBarrios as $key => $barrio) {
                        $id = $barrio->idbarrio;
                        //dump($id);
                        $estaEnBarrios = $coleccionBarrios->contains(function ($barrio) use ($id) {
                            return $barrio->id_barrio == $id;
                        });    
                        //dump($estaEnBarrios);
                        if($estaEnBarrios == false){
                            
                            $nuevobarrio = $this->validateBarrio((double)$suma,$id);
                            //dump($nuevobarrio);
                            if( is_array( $nuevobarrio )){
                                $data_barrios->barrios[] = (object)$nuevobarrio;    
                                
                                $savetrue = true;
                            }
                            
                            $arr_group = session('arr_group');
                            $arr_group[] = $request->grupo;
                            session(['arr_group' => $arr_group ]);
                            
                        }
                    }

                    
                }
                //dd($savetrue);
                
                if(isset($request->cuit)){
                    $id = $request->cuit;
                    $estaEnBarrios = $coleccionBarrios->contains(function ($barrio) use ($id) {
                        return $barrio->id_barrio == $id;
                    });
                    
                    if($estaEnBarrios == false){
                        
                        $nuevobarrio = $this->validateBarrio((double)$suma,$id);
                        
                        if( is_array( $nuevobarrio )){
                            $data_barrios->barrios[] = (object)$nuevobarrio;    
                            $savetrue = true;
                        }else if($nuevobarrio == 2){
                            return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id, 'error_cuit' => 2, 'cuit' => $request->cuit]);            
                        }else{
                            return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id, 'error_cuit' => "El CUIT $request->cuit no existe", 'cuit' => $request->cuit]);            
                        }
                        
                    }else{
                        return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id, 'error_cuit' => "El CUIT $request->cuit ya se encuentra entre las claúsulas de tu propuesta", 'cuit' => $request->cuit]);        
                    }
                }
                
                if($savetrue){
                    

                    if(isset($request->grupo)){
                        $res = Propuesta::where('prefijo', $request->prefijo)
                                    ->where('idpropuesta', $request->id)
                                    ->update(['data_barrios' => json_encode($data_barrios)]);
                        if($res > 0){
                            $propuesta = Propuesta::where('idpropuesta', $request->id)->where('prefijo',$request->prefijo)->first();
                            Cola::create([
                                'entity' => 'propuestas',
                                'entity_id' => $propuesta->id,
                                'codempresa' => $propuesta->codempresa,
                            ]);
                        }
                        return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id, 'success_grupo' => $request->grupo]);        
                    }
                    if(isset($request->cuit)){
                        if(!empty($nuevobarrio)){
                            $res = Propuesta::where('prefijo', $request->prefijo)
                            ->where('idpropuesta', $request->id)
                            ->where('cobertura_suma', '>=',$nuevobarrio['sumamuerte'])
                            ->update(['data_barrios' => json_encode($data_barrios)]);
                            if($res > 0){
                                $propuesta = Propuesta::where('idpropuesta', $request->id)->where('prefijo',$request->prefijo)->first();
                                Cola::create([
                                    'entity' => 'propuestas',
                                    'entity_id' => $propuesta->id,
                                    'codempresa' => $propuesta->codempresa,
                                ]);
                            }
                            return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id, 'success_barrio' => $request->cuit]);        
                        }
                    }

                }else{
                    return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id, 'error_grupo' => 'No se ha agregado ningún barrio con el grupo seleccionado']);        
                }
                
            }
            
            return redirect()->route("agregar_barrios", ['prefijo' => $request->prefijo , 'idpropuesta' => $request->id]);  

        }catch (Exception $ex){
            $error = new logs();
            $error->saveerror($ex->getMessage(), "", "", "ADDBARRIO 101");
            return redirect()->route("polizas");
        }
        

    }


    public function validateBarrio($suma, $idBarrio){
        $aplica = barrio::where('id',$idBarrio)->where('suma_muerte','<=',$suma)->first();
        
        if($aplica){
            $nuevobarrio = [
                "id" => null,
                "id_propuesta" => null,
                "id_barrio" => $aplica->id."",
                "nombre" => $aplica->nombre,
                "ultmod" => null,
                "user_edit" => null,
                "codestado" => null,
                "prefijo" => null,
                "idprefijo" => null,
                "codempresa" => null,
                "sumamuerte" => $aplica->suma_muerte,
                "sumagm" => $aplica->suma_gm,
                "email" => $aplica->email
            ];
            return $nuevobarrio;
        }

        $aplica = barrio::where('id',$idBarrio)->where('suma_muerte','>',$suma)->first();
        if($aplica){
            return 2;
        }

        return FALSE;
    }

    public function getConsolidated($date,$codempresa){
        $registerPay = payregistry::leftJoin('propuestas', function($join){
                $join->on('payregistries.idpropuesta', '=', 'propuestas.idpropuesta')
                ->on('payregistries.prefijo', '=', 'propuestas.prefijo');
        })
        ->select('payregistries.*','propuestas.id as idprop')
        ->where('payregistries.fecha_paga','>',$date.' 00:00:01')->where('payregistries.fecha_paga','<',$date.' 23:59:59')
        ->where('propuestas.codempresa',$codempresa)
        ->where('codestado','>',0)
        ->orderBy('propuestas.id','ASC')
        ->get();
        $proposalPays = Propuesta::where('codestado','>',0)
        ->where('codempresa',$codempresa)
        ->where('formadepago','CREDITO')
        ->where('paga',1)
        ->where('fecha_paga','>',$date.' 00:00:01')
        ->where('fecha_paga','<',$date.' 23:59:59')
        ->orderBy('propuestas.id','ASC')
        ->get();

        $datPay = [];
        foreach ($registerPay as $key => $value) {
            $datPay[] = $value->idprop;
        }
        
        $datPro = [];
        foreach ($proposalPays as $key => $value) {
            $datPro[] = $value->id;
        }
        $valuesDiff = [];
        try {
            $valuesDiff = array_values( array_diff($datPay,$datPro));
        } catch (\Throwable $th) {
            //throw $th;
        }

        $restUpdate = 0;
        if(!empty( $valuesDiff) ){
            $restUpdate = DB::table('propuestas')
            ->join('payregistries', function ($join){
                $join->on( 'propuestas.idpropuesta', 'payregistries.idpropuesta')
                     ->on( 'propuestas.prefijo', 'payregistries.prefijo');
            })
            ->whereIn('propuestas.id', $valuesDiff )
            ->update([
                'propuestas.paga' => 1,
                'propuestas.fecha_paga' => DB::raw('payregistries.fecha_paga'),
                'propuestas.tipopago' => DB::raw('payregistries.tipopago'),
                'propuestas.compformadepago' => DB::raw('payregistries.compformadepago'),
                'propuestas.valor_pagado' => DB::raw('payregistries.valor_pagado'),
                'propuestas.fecha_comprobante' => DB::raw('payregistries.fecha_comprobante'),
                'propuestas.version' => DB::raw('(propuestas.version + 1)'),
            ]);
            foreach ($valuesDiff as $key => $value) {
                Cola::create([
                    'entity' => 'propuestas',
                    'entity_id' => $value,
                    'codempresa' => $codempresa,
                ]);
            }
        }

        $reportRest = Propuesta::select("id","reg","documento","nombre","num_polizas","meses","id_cobertura","id_barrio","nueva_poliza","premio","premio_total","fechaDesde","fechaHasta","clausula","barrio_beneficiario","ultmod","useredit as user_edit","codestado","cobertura_suma","cobertura_deducible","cobertura_gastos","promocion","paga","fecha_paga","referencia","prima","master","organizador","productor","puntodeventa","prefijo","updated_at","created_at","formadepago","usuariopaga","tipopago","compformadepago","csrf","fecha_nacimiento","codempresa","idpropuesta","nota","data_barrios","version","valor_pagado","imputacion","fecha_comprobante")->where('fecha_paga','>',$date.' 00:00:01')
                                ->where('fecha_paga','<',$date.' 23:59:59')
                                ->where('codempresa',$codempresa)
                                ->get();
        
        $idsPropuesta =  $reportRest->map(function($pro) {
            return $pro->id;
        })->toArray();

        $idsClientes =  $reportRest->map(function($pro) {
            return $pro->documento;
        })->toArray();

        $lineas = LineasPropuesta::join('propuestas', function ($join){
                                        $join->on( 'propuestas.idpropuesta', 'lineas_propuestas.id_propuesta')
                                            ->on( 'propuestas.prefijo', 'lineas_propuestas.prefijo');
                                    })
                                    ->whereIn('propuestas.id',$idsPropuesta)
                                    ->select('lineas_propuestas.*')
                                    ->orderBy('propuestas.id','ASC')
                                    ->get();

        $idsClientes_ =  $lineas->map(function($lin) {
            return $lin->documento;
        })->toArray();
        
        $clientes = cliente::whereIn('id',array_unique(array_merge($idsClientes,$idsClientes_)))->groupBy('id')->get();

        $data = [
            'cantReg' => $registerPay->count(),
            'cantProp' => $proposalPays->count(),
            'diff' => $valuesDiff,
            'restupd' => $restUpdate,
            'report' => [
                'propuestas' => $reportRest,
                'lineas' => $lineas,
                'clientes' => $clientes
            ]
        ];
        
        //revisar que todos los pagos se hayan registrado en las propuestas
        //sino, entonces voy a registrar los pagos en las propuestas y sumar la version
            //si existen pagos no registrados, entonces hacer un registro de cola de las propuestas
        //al final generaré una consulta con todas las propuestas, las líneas propuestas y los clientes
        return response()->json($data);
    }

    /**
     * Function for get proposal, clients, groups and lines for date
     * @param string $date
     *  Date for proposal
     * @param string $codempresa
     *  Company
     * @param string $prefix
     *  Prefix
     * @return json
     */
    public function getDateProposal($date, $codempresa, $prefix){
        
        $proposal = Propuesta::select("id","reg","documento","nombre","num_polizas","meses","id_cobertura","id_barrio","nueva_poliza","premio","premio_total","fechaDesde","fechaHasta","clausula","barrio_beneficiario","ultmod","useredit as user_edit","codestado","cobertura_suma","cobertura_deducible","cobertura_gastos","promocion","paga","fecha_paga","referencia","prima","master","organizador","productor","puntodeventa","prefijo","updated_at","created_at","formadepago","usuariopaga","tipopago","compformadepago","csrf","fecha_nacimiento","codempresa","idpropuesta","nota","data_barrios","version","valor_pagado","imputacion","fecha_comprobante")->where(function($query) use ($date) {
                                    $query->where('fecha_paga', '>', $date.' 00:00:01')
                                    ->where('fecha_paga', '<', $date.' 23:59:59');
                                })
                                ->orWhere(function($query) use ($date) {
                                    $query->where('ultmod', '>', $date.' 00:00:01')
                                        ->where('ultmod', '<', $date.' 23:59:59');
                                })
                                ->where('codempresa', $codempresa)
                                ->where('prefijo','!=',$prefix)
                                ->get();
                                
        $idsPropuesta =  $proposal->map(function($pro) {
            return $pro->id;
        })->toArray();

        $idsClientes =  $proposal->map(function($pro) {
            return $pro->documento;
        })->toArray();
        
        $lineas = LineasPropuesta::join('propuestas', function ($join){
                        $join->on( 'propuestas.idpropuesta', 'lineas_propuestas.id_propuesta')
                            ->on( 'propuestas.prefijo', 'lineas_propuestas.prefijo');
                    })
                    ->whereIn('propuestas.id',$idsPropuesta)
                    ->select('lineas_propuestas.*')
                    ->orderBy('propuestas.id','ASC')
                    ->get();

        $idsClientes_ =  $lineas->map(function($lin) {
            return $lin->documento;
        })->toArray();

        $clientes = cliente::whereIn('id',array_unique(array_merge($idsClientes,$idsClientes_)))->groupBy('id')->get();

        $data = [
            'cantProp' => $proposal->count(),
            'report' => [
                'propuestas' => $proposal,
                'lineas' => $lineas,
                'clientes' => $clientes
            ]
        ];

        return response()->json($data);
    }

    
}
