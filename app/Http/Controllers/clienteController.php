<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use App\Models\Cola;
use App\Models\logs;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class clienteController extends Controller
{
    //
    public function consultaparametros()
    {
        $req = request()->all();


        if ($req["id"] != null) {
            
            $cliente = new cliente();
            
            $cliente->id = $req["documentotomador"];
            $cliente->nombres = $req["nombrestomador"];
            $cliente->apellidos = $req["apellidostomador"];
            $cliente->tipo_id = $req["tipodocumentotomador"];
            $cliente->telefono = $req["telefonotomador"];
            $cliente->direccion = $req["direcciontomador"];
            $cliente->email = $req["emailtomador"];
            $cliente->codpostal = $req["codpostaltomador"];
            $cliente->localidad = $req["localidadtomador"];
            $cliente->ciudad = $req["ciudadtomador"];
            $cliente->sexo = $req["sexotomador"];
            $cliente->fecha_nacimiento = $req["fechanacimientotomador"];
            $cliente->situacion = $req["situaciontomador"];
            $cliente->ultmod = date("Y-m-d H:i:s");
            $cliente->user_edit = "NUBE";
            $cliente->codestado = 1;
            
            if ($cliente->save()) {
                Cola::create([
                    'entity' => 'clientes',
                    'entity_id' => $cliente->reg,
                    'codempresa' => $cliente->codempresa,
                ]);
                return response()->json("Se ha creado el cliente con éxito", 202);
            } else {
                return response()->json("Hubo un error al crear el cliente", 404);
            }

        }
    }

    /**
     * function return data client if math
     */
    public function getClient(Request $req) : JsonResponse {
        $client = cliente::where('id',$req['document'])->where('codestado',1)->first();
        $data = ['success' => FALSE];
        if(!empty($client)){
            $data = [
                "nombres" => $client->nombres,
                "apellidos" => $client->apellidos,
                "tipo_id" => $client->tipo_id,
                "fecha_nacimiento" => $client->fecha_nacimiento,
                "telefono" => $client->telefono,
                "direccion" => $client->direccion,
                "codpostal" => $client->codpostal,
                "success" => TRUE
            ];
        }

        return response()->json($data);
    }

    /**
     * function for create client
     */
    public function createClientInsured(Request $req) : JsonResponse {
        $data = ['success' => FALSE];
        $insureds = $req['insureds'];
        $codempresa = $req['codempresa'];
        $currentDate = now('America/Argentina/Buenos_Aires');
        $currentDateStr = $currentDate->format('Y-m-d H:i:s');
        $insureds_gen = explode(';',$insureds);
        try {
            foreach ($insureds_gen as $key => $insured) {
                $insured_expl = explode(',',$insured);
                if(count($insured_expl) < 5)
                    return response()->json(['success' => FALSE, 'message' => "Los datos $insured no están completos, deberían ser 5 datos"]);
                foreach ($insured_expl as $k => $val) {
                    if(empty(trim($val)))
                        return response()->json(['success' => FALSE, 'message' => "Los datos de $insured tienen campos vacíos"]);
                }
                if($this->validateDate($insured_expl[4]) == FALSE)
                    return response()->json(['success' => FALSE, 'message' => "La fecha $insured_expl[4] es incorrecta, el formato correcto es yyyy-mm-dd"]);
                $client = cliente::where('id',$insured_expl[2])->first();
                if($client){
                    if(empty($client->fecha_nacimiento)) {
                        $client->nombres = trim($insured_expl[0]);
                        $client->apellidos = trim($insured_expl[1]);
                        $client->tipo_id = trim($insured_expl[3]);
                        $client->fecha_nacimiento = trim($insured_expl[4]);
                        $client->codestado = 1;
                        $client->codempresa = trim($codempresa);
                        $client->ultmod = $currentDateStr;
                        $client->user_edit = "online";
                        $client->save();
                    }
                }else{
                    $client = cliente::create([
                        'id' => trim($insured_expl[2]),
                        'nombres' => trim($insured_expl[0]),
                        'apellidos' => trim($insured_expl[1]),
                        'tipo_id' => trim($insured_expl[3]),
                        'fecha_nacimiento' => trim($insured_expl[4]),
                        'codestado' => 1,
                        'codempresa' => trim($codempresa),
                        'ultmod' => $currentDateStr,
                        'user_edit' => "online",
                    ]);
                }
                Cola::create([
                    'entity' => 'clientes',
                    'entity_id' => $client->id,
                    'codempresa' => $codempresa,
                    'ptoventa' => 'O',
                ]);
            }
            $data['success'] = TRUE;
        } catch (\Exception $e) {
            $data['success'] = FALSE;
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    /**
     * function for create client
     */
    public function createClientTaker(Request $req) : JsonResponse {
        
        
        
        try {
            $data = ['success' => FALSE];
            
            $dat["codempresa"] = "codempresa:".$req["codempresa"];
            $dat["nombres"] = "nombres:".$req["nombres"];
            $dat["apellidos"] = "apellidos:".$req["apellidos"];
            $dat["tipo_id"] = "tipo_id:".$req["tipo_id"];
            $dat["fecha_nacimiento"] = "fecha_nacimiento:".$req["fecha_nacimiento"];
            $dat["telefono"] = "telefono:".$req["telefono"];
            $dat["codpostal"] = "codpostal:".$req["codpostal"];
            $dat["email"] = "email:".$req["email"];
            $dat["id"] = "id:".$req["id"];
            $dat["sexo"] = "id:".$req["sexo"];
            $error = new logs();
            $error->saveerror(implode(";", $dat), "", "", "JSTaker");

            $codempresa = $req['codempresa'];
            $currentDate = now('America/Argentina/Buenos_Aires');
            $currentDateStr = $currentDate->format('Y-m-d H:i:s');
            if( empty( trim($req['nombres'])) 
                || empty( trim($req['apellidos'])) 
                || empty( trim($req['tipo_id'])) 
                || empty( trim($req['fecha_nacimiento'])) 
                || empty( trim($req['telefono'])) 
                || empty( trim($req['codpostal']))
            )
                return response()->json(['success' => FALSE, 'message' => 'Nombres, Apellidos, Tipo ID, Fecha Nacimiento, Teléfono y Codpostal no pueden estar vacíos']);

            if(!empty(trim($req['email']))){
                if(!filter_var(trim($req['email']),FILTER_VALIDATE_EMAIL))
                    return response()->json(['success' => FALSE, 'message' => 'Correo electrónico inválido']);
            }
                $client = cliente::where('id',$req['id'])->first();
                if($client){
                        $client->nombres = trim($req['nombres']);
                        $client->apellidos = trim($req['apellidos']);
                        $client->tipo_id = trim($req['tipo_id']);
                        $client->fecha_nacimiento = trim($req['fecha_nacimiento']);
                        $client->telefono = trim($req['telefono']);
                        $client->direccion = trim($req['direccion']);
                        $client->email = trim($req['email']);
                        $client->codpostal = trim($req['codpostal']);
                        $client->sexo = trim($req['sexo']);
                        $client->codestado = 1;
                        $client->codempresa = trim($codempresa);
                        $client->ultmod = $currentDateStr;
                        $client->user_edit = "online";
                        $client->save();
                }else{
                    $client = cliente::create([
                        'id' => trim($req['id']),
                        'nombres' => trim($req['nombres']),
                        'apellidos' => trim($req['apellidos']),
                        'tipo_id' => trim($req['tipo_id']),
                        'fecha_nacimiento' => trim($req['fecha_nacimiento']),
                        'telefono' => trim($req['telefono']),
                        'direccion' => trim($req['direccion']),
                        'email' => trim($req['email']),
                        'codpostal' => trim($req['codpostal']),
                        'sexo' => trim($req['sexo']),
                        'codestado' => 1,
                        'codempresa' => trim($codempresa),
                        'ultmod' => $currentDateStr,
                        'user_edit' => "online",
                    ]);
                }
                Cola::create([
                    'entity' => 'clientes',
                    'entity_id' => $client->id,
                    'codempresa' => $codempresa,
                    'ptoventa' => 'O',
                ]);
            $data['success'] = TRUE;
        } catch (\Exception $e) {
            $data['success'] = FALSE;
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    public function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }


    /**
     * function validate exist client
     */
    public function getInsured(Request $req) : JsonResponse {
        $data = ['success' => FALSE];
        if(!empty($req['documents'])){
            $arrayDni = explode(',',$req['documents']);
            if(!empty( $arrayDni ))
                $arrayDni = array_unique($arrayDni);
            $clients = cliente::whereIn('id',$arrayDni)->groupBy('id')->get();
            $arrFound = [];
            if(!empty($clients)){
                $data['success'] = TRUE;
                foreach ($clients as $key => $client) {
                    $data['documentsFound'] = !empty($data['documentsFound']) ? $data['documentsFound'].','.$client->id : $client->id;
                    $data['namesFound'] = !empty($data['namesFound']) ? $data['namesFound'].','."$client->id $client->nombres $client->apellidos" : "$client->id $client->nombres $client->apellidos";
                    $arrFound[] = $client->id;
                }
                foreach ($arrayDni as $key => $arr) {
                    if(!in_array($arr,$arrFound)){
                        $data['documentsNotFound'] = !empty($data['documentsNotFound']) ? $data['documentsNotFound'].','.$arr : $arr;
                        $data['success'] = FALSE;
                    }
                }
            }
        }   
        return response()->json($data);
    }
}
