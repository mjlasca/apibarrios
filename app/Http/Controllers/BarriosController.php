<?php

namespace App\Http\Controllers;

use App\Models\barrio;
use App\Models\Cobertura;
use App\Models\gruposbarrio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BarriosController extends Controller
{
    public function validateCuits(Request $req) : JsonResponse {
        $data = ['success' => FALSE];
        if(!empty($req['cuits'])){
            $terminos = explode(',', $req['cuits']);
            $terminos = array_map('trim', $terminos); // Limpiar espacios
            $terminos = array_filter($terminos); // Eliminar vacíos
            
            $noEncontrados = []; // Para acumular los que no se encuentran
            $resultado = [];     // Para almacenar el detalle de cada término
            
            foreach ($terminos as $termino) {
                if (is_numeric($termino)) {
                    $barrio = barrio::where('id', $termino)->first();
                } else {
                    $barrio = barrio::where('nombre', 'LIKE', '%' . $termino . '%')->first();
                }
            
                if (!$barrio) {
                    $noEncontrados[] = $termino;
                }
            
                $resultado[] = $barrio->id;
            }
            
            // Concatenar los no encontrados en una cadena
            $cadenaNoEncontrados = implode(', ', $noEncontrados);
            if(!empty($cadenaNoEncontrados)){
                $data['cadenaNoEncontrados'] = $cadenaNoEncontrados;
            }else{
                $data['success'] = TRUE;
                $neighbours = barrio::whereIn('id', $resultado)
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
                $data['cobertura'] = "Cobertura $coverage->nombre, Suma : $coverage->suma , Vr. Mensual : $coverage->vrMensual";
                $data['cuits'] = implode(',',$resultado);
            }
                
        }
        return response()->json($data);
    }
}
