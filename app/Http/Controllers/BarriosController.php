<?php

namespace App\Http\Controllers;

use App\Models\barrio;
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
            
                $resultado[] = [
                    'valor' => $termino,
                    'encontrado' => $barrio ? true : false,
                    'barrio' => $barrio
                ];
            }
            
            // Concatenar los no encontrados en una cadena
            $cadenaNoEncontrados = implode(', ', $noEncontrados);
            $data['cadenaNoEncontrados'] = $cadenaNoEncontrados;
        }
        return response()->json($data);
    }
}
