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
            $arrayCuits = explode(',',$req['cuits']);
            $barrios = barrio::whereIn('id',$arrayCuits)->pluck('id')->toArray();
            $arrayCuits = array_diff($arrayCuits, $barrios);
            if(empty($arrayCuits)){
                $data['success'] = TRUE;
            }else{
                $data['cuits_faltantes'] = implode(',',array_unique($arrayCuits));
            }
        }
        return response()->json($data);
    }
}
