<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use App\Models\Cola;
use Illuminate\Http\Request;

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
    public function getClient(Request $req) {
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

        return json_encode($data);
    }

    /**
     * function validate exist client
     */
    public function getInsured(Request $req) {
        $data = ['success' => FALSE];
        if(!empty($req['documents'])){
            $arrayDni = explode(',',$req['documents']);
            $clients = cliente::whereIn('id',$arrayDni)->get();
            if(!empty($clients)){
                $data['success'] = TRUE;
                foreach ($clients as $key => $client) {
                    $data['dni'.$client->id] = [
                        'documento' => $client->id,
                        'tipo' => $client->tipo_id,
                        'nombres' => $client->nombres,
                        'apellidos' => $client->apellidos,
                        'fecha_nacimiento' => $client->fecha_nacimiento,
                    ];
                }
                foreach ($arrayDni as $key => $arr) {
                    if(!isset($data['dni'.$arr])){
                        $data['dni'.$arr] = "No existe el documento ".$arr;
                        $data['success'] = FALSE;
                    }
                }
            }
        }   
        return json_encode($data);
    }
}
