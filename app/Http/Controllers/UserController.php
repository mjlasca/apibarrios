<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //

    public function confirmarpuntodeventa(){
        $req = request()->all();

        $cons = DB::table('users')->where('api_token',$req['api_token'])->get();
        
        if(count($cons) > 0){
            $cons['res'] = "Token confirmado";
            return response()->json($cons, 200);
        }
        else
            return response()->json(['res' => 'El token no existe'], 400);
            
    }

    public function editarpuntodeventa(){
        $req = request()->all();
        $result = $this->savepuntos($req);
        if ($result != "")
            return response()->json(['res' => 'Hay errores al tratar de procesar los puntos de venta ' . $result], 400);
        else
            return response()->json(['res' => 'Se ha actulizado los puntos de venta con éxito'], 200);
    }

    private function savepuntos($req)
    {
        $errores = "";
        if ($req["listpuntosdeventa"] != null) {
            
            foreach ($req["listpuntosdeventa"] as $value) {
                if($value['rol'] != "PRINCIPAL"){
                    $user = new User();
                    $cons = $data = DB::table('users')->where('email',$value['usuario'])->get();
                    if(count($cons) > 0){
                        $user->where('email',$value['usuario'])->update([
                            "name" => $value["nombre"],
                            "prefijo" => $value["prefijo"],
                            "rol" => $value["rol"],
                            "api_token" => $value["apitoken"]
                        ]);
                    }else{
                        $user->name = $value["nombre"];
                        $user->prefijo = $value["prefijo"];
                        $user->rol = $value["rol"];
                        $user->api_token     = $value["apitoken"];
                        $user->email = $value["usuario"];
                        $user->password = sha1($user->name);
                        
                        if (!$user->save()) {
                            $errores .= "No se pudo guardar el registro con usuario " . $user->email;
                        }
                    }
                }
            }
        }

        return $errores;
    }
}
