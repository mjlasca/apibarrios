<?php

namespace App\Http\Controllers;

use App\Models\barrio;
use App\Models\Cobertura;
use App\Models\gruposbarrio;
use App\Models\logs;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BarriosController extends Controller
{
    public function validateCuits(Request $req) : JsonResponse {
        try {
            $error = new logs();
            $error->saveerror($req->getContent(), "", "", "JSON Barr");
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
                        $stopWords = ['de', 'la', 'el', 'los', 'las','la'];
                        $words = array_filter(explode(" ",$termino), fn($word) => !in_array($word, $stopWords));
                        $query = barrio::query();
                        foreach ($words as $word) {
                            $query->whereRaw('LOWER(nombre) LIKE ?', ["%$word%"]);
                        }
                        $barrio = $query->WhereNotNull('suma_muerte')->orderBy('suma_muerte', 'desc')->first();
                    }
                
                    if (!$barrio) {
                        $noEncontrados[] = $termino;
                    }
                
                    if(!empty($barrio))
                        $resultado[] = $barrio->id;
                }
                
                // Concatenar los no encontrados en una cadena
                $cadenaNoEncontrados = implode(', ', $noEncontrados);
                if(!empty($cadenaNoEncontrados)){
                    $data['cadenaNoEncontrados'] = $cadenaNoEncontrados;
                }else{
                    
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
                    if(!empty($coverage)){
                        $data['success'] = TRUE;
                        $data['cobertura'] = $coverage->nombre;
                        $data['cobertura_info'] = "Cobertura $coverage->nombre, Suma : $coverage->suma , Vr. Mensual : $coverage->vrMensual";
                        $data['cuits'] = implode(',',$resultado);
                    }else{
                        $data['error'] = 'No hay una cobertura disponible para la suma muerte : $'. number_format( $neighbours->max('suma_muerte') );
                    }
                    
                }
                    
            }
            return response()->json($data);
        } catch (\Throwable $th) {
            $data['success'] = FALSE;
            $data['error'] = $req->getContent()." | ".$th->getMessage();
            $error = new logs();
            $error->saveerror(implode(";", $data), "", "", "JSON ERR Barr");
            return response()->json($data,500);
        }
        
    }
}
