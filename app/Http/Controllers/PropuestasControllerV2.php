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
use App\Models\Clasificacione;
use App\Models\Actividade;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Cobertura;



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

    public function downloadAll($id,$prefijo){
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

                $cliente = DB::table('clientes')->where('id',$data[0]->documento)->get();
                
                $pdf = PDF::loadView('pdf-all.index', compact('cliente','data','lineasdata','barriospropuesta'));
                /*$dompdf = $pdf->getDomPDF();
                $canvas = $dompdf->getCanvas();

                // Aplica la contraseña: usuario, propietario, permisos
                $canvas->get_cpdf()->setEncryption(
                    $cliente[0]->id, // Contraseña de usuario (para abrir el PDF)
                    $cliente[0]->id, // Contraseña de propietario (para controlar permisos)
                    [
                        'print' => true,
                        'modify' => false,
                        'copy' => false,
                        'annot-forms' => false,
                    ]
                );*/
                return $pdf->stream();
            } else {
                return response()->json(['res' => 'El documento solicitado no existe o no se ha generado en la nube'], 400);
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

        $reportRest = Propuesta::select("id","reg","documento","nombre","num_polizas","meses","id_cobertura","id_barrio","nueva_poliza","premio","premio_total","fechaDesde","fechaHasta","clausula","barrio_beneficiario","ultmod","useredit as user_edit","codestado","cobertura_suma","cobertura_deducible","cobertura_gastos","promocion","paga","fecha_paga","referencia","prima","master","organizador","productor","puntodeventa","prefijo","updated_at","created_at","formadepago","usuariopaga","tipopago","compformadepago as compformapago","csrf","fecha_nacimiento","codempresa","idpropuesta","nota","data_barrios","version","valor_pagado","imputacion","fecha_comprobante")->where('fecha_paga','>',$date.' 00:00:01')
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
    public function getDateProposal($date, $codempresa, $prefix, $idpropuesta = null){
        
        $proposal = Propuesta::select("id","reg","documento","nombre","num_polizas","meses","id_cobertura","id_barrio","nueva_poliza","premio","premio_total","fechaDesde","fechaHasta","clausula","barrio_beneficiario","ultmod","useredit as user_edit","codestado","cobertura_suma","cobertura_deducible","cobertura_gastos","promocion","paga","fecha_paga","referencia","prima","master","organizador","productor","puntodeventa","prefijo","updated_at","created_at","formadepago","usuariopaga","tipopago","compformadepago as compformapago","csrf","fecha_nacimiento","codempresa","idpropuesta","nota","data_barrios","version","valor_pagado","imputacion","fecha_comprobante")->where('codempresa', $codempresa)
                            ->where('prefijo','!=',$prefix)
                            ->where(function($query) use ($date) {
                                    $query->where('fecha_paga', '>', $date.' 00:00:01')
                                    ->where('fecha_paga', '<', $date.' 23:59:59')
                                    ->orWhere(function($query0) use ($date) {
                                        $query0->where('ultmod', '>', $date.' 00:00:01')
                                            ->where('ultmod', '<', $date.' 23:59:59');
                                    });
                                })
                            ->get();
        if(!empty($idpropuesta)){
            $proposal = Propuesta::select("id","reg","documento","nombre","num_polizas","meses","id_cobertura","id_barrio","nueva_poliza","premio","premio_total","fechaDesde","fechaHasta","clausula","barrio_beneficiario","ultmod","useredit as user_edit","codestado","cobertura_suma","cobertura_deducible","cobertura_gastos","promocion","paga","fecha_paga","referencia","prima","master","organizador","productor","puntodeventa","prefijo","updated_at","created_at","formadepago","usuariopaga","tipopago","compformadepago as compformapago","csrf","fecha_nacimiento","codempresa","idpropuesta","nota","data_barrios","version","valor_pagado","imputacion","fecha_comprobante")
            ->where('prefijo',$prefix)
            ->where('idpropuesta',$idpropuesta)
            ->where('codempresa', $codempresa)
            ->get();
        }
        
                                
                            
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

    public function CreateProposalChat(Request $req) : JsonResponse {
        $data = ['success' => FALSE];
        try {
            $clasification = Clasificacione::where('cod',$req['cod_clasificacion'])->first();
            $activity = Actividade::where('cod',$req['cod_actividad'])->first();
            if(empty($clasification) || empty($activity)){
                return response()->json(['success' => FALSE, 'message' => 'No se encontró la clasificación o actividad']);
            }
            $currentDate = now('America/Argentina/Buenos_Aires');
            $currentDateStr = $currentDate->format('Y-m-d H:i:s');
            $toDate = $currentDate;
            $proposal = new Propuesta();
            $mainClient = cliente::where('id',$req['tomador'])->where('codestado',1)->first();
            $data['idpropuesta'] = $proposal->consecutivo("O");
            $data['prefijo'] = "O";
            dump(explode(",", $req["asegurados"]));
            DB::enableQueryLog(); // Activa el log
            $ids = array_map('intval', explode(",", $req["asegurados"]));

            $insureds = cliente::whereIn('reg', $ids)
                ->where('codestado', 1)
                ->groupBy('reg')
                ->orderBy('reg', 'ASC')
                ->get();

            dump(DB::getQueryLog());
            dump($insureds);
            $numPolizas = count($insureds);
            dd($numPolizas);
            if($numPolizas == 0)
                return response()->json(['success' => FALSE, 'message' => 'O no se envió asegurados o no existen los que se enviaron']);

            $groups = gruposbarrio::whereIn('idbarrio', explode(",", $req["cuits"]))->where("codestado",1)->whereNotIn('nombre', explode(",", $req["lista_grupos_descartar"]))->groupBy('nombre')->pluck('id')->toArray();
            $neighboursGroup = gruposbarrio::whereIn('id', $groups)->where("codestado",1)->where('idbarrio','!=',NULL)->where('idbarrio','!=','')->groupBy('idbarrio')->pluck('idbarrio')->toArray();
            $neighbours = barrio::whereIn('id', $neighboursGroup)
                ->where("codestado",1)
                ->whereNotNull('suma_muerte')
                ->where('suma_muerte','!=','')
                ->whereNotNull('id')
                ->where('id','!=','')
                ->groupBy('id')
                ->get();
            $coverage = Cobertura::where('suma', '>=', $neighbours->max('suma_muerte'))
                        ->where('codestado', 1)
                        ->orderBy('suma', 'asc')
                        ->first();
            $prize = $coverage->vrMensual;
            $prize_sum = $coverage->vrMensual * $req['meses'];
            $prizeTotal = 0;
            $promo = "";
            if($req['meses'] == 2 && !empty($coverage->x21)){
                $prize_sum = $coverage->x21;
                $promo = "2x1";
            }
            if($req['meses'] == 3 && !empty($coverage->x32)){
                $prize_sum = $coverage->x32;
                $promo = "3x2";
            }
            if($req['meses'] == 6 && !empty($coverage->x64)){
                $prize_sum = $coverage->x64;
                $promo = "6x4";
            }
            if($insureds){
                foreach ($insureds as $key => $client) {
                    $forEge =  $this->ageCalculate($client->fecha_nacimiento) > 60 ? ($prize_sum * 2) : $prize_sum;
                    $prizeTotal += $forEge;
                }
            }
            $arrayNeighbours['barrios'] = [];
            $neighbours->map(function($item) use (&$arrayNeighbours) {
                $arrayNeighbours['barrios'][] = [
                    'id' => null,
                    'id_propuesta' => null,
                    'id_barrio' => $item->id,
                    'nombre' => $item->nombre,
                    'ultmod' => null,
                    'user_edit' => null,
                    'codestado' => null,
                    'prefijo' => null,
                    'idprefijo' => null,
                    'codempresa' => "",
                    'sumamuerte' => $item->suma_muerte,
                    'sumagm' => $item->suma_gm,
                    'email' => null,
                ];
            });
            
            $proposal = Propuesta::create([
                'codempresa' => $req['codempresa'],
                'prefijo' => "O",
                'idpropuesta' => $data['idpropuesta'],
                'reg' => $data['idpropuesta'],
                'codestado' => 1,
                'documento' => $mainClient->id,
                'nombre' => "$mainClient->nombres $mainClient->apellidos",
                'num_polizas' => $numPolizas,
                'meses' => $req['meses'],
                'id_cobertura' => $coverage->nombre,
                'id_barrio' => $req['idpropuesta'],
                'nueva_poliza' => 1,
                'premio' => $prize,
                'premio_total' => $prizeTotal,
                'fechaDesde' => $currentDateStr,
                'fechaHasta' => $toDate->modify("+".$req['meses']." months")->format('Y-m-d H:i:s'),
                'ultmod' => $currentDateStr,
                'useredit' => "online",
                'cobertura_suma' => $coverage->suma,
                'cobertura_deducible' => $coverage->deducible,
                'cobertura_gastos' => $coverage->gastos,
                'promocion' => $promo,
                'paga' => 0,
                'fecha_paga' => "1000-01-01 00:00:00",
                'master' => $req['master'],
                'organizador' => $req['organizador'],
                'productor' => $req['productor'],
                'data_barrios' => json_encode($arrayNeighbours),
                'version' => 1,
                'fecha_comprobante' => "1000-01-01",
                'fecha_nacimiento' => $mainClient->fecha_nacimiento,
                'formadepago' => "CREDITO",
            ]);

            if($proposal){
                foreach ($insureds as $key => $client) {
                    $line = new LineasPropuesta();
                    $line->id_propuesta = $data['idpropuesta'];
                    $line->prefijo = "O";
                    $line->codempresa = $req['codempresa'];
                    $line->documento = $client->id;
                    $line->tipo_documento = $client->tipo_documento;
                    $line->apellidos = $client->apellidos;
                    $line->nombres = $client->nombres;
                    $line->fecha_nacimiento = $client->fecha_nacimiento;
                    $line->id_actividad = $activity->reg;
                    $line->id_clasificacion = $clasification->reg;
                    $line->premio = $prize;
                    $line->ultmod = $proposal->ultmod;
                    $line->user_edit = "online";
                    $line->codestado = 1;
                    $line->fechaDesde = $proposal->fechaDesde;
                    $line->actividad = $activity->nombre;
                    $line->clasificacion = $clasification->nombre;
                    $line->fechaHasta = $proposal->fechaHasta;
                    $line->save();
                }
                Cola::create([
                    'entity' => 'propuestas',
                    'entity_id' => $proposal->id,
                    'codempresa' => $req['codempresa'],
                    'ptoventa' => 'O',
                ]);
                $data['success'] = TRUE;
            }
        } catch (\Throwable $e) {
            $data['success'] = FALSE;
            $data['error'] = $e->getMessage();
        }
        
        
        return response()->json($data);
    }

    /**
     * Return age
     *
     * @param [type] $birthDate
     * @return void
     */
    public function ageCalculate($birthDate) {
        $birthDate = new \DateTime($birthDate);
        $today = new \DateTime();
        $age = $birthDate->diff($today)->y;
        return $age;
    }
    
}
