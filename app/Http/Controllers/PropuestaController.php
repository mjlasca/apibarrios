<?php

namespace App\Http\Controllers;

use App\Models\Propuesta;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class PropuestaController extends Controller
{
    //
    public function __invoke()
    {

        //return response()->json( $get, 200);
    }


    public function descargapdfpoliza()
    {
        $data = request()->all();

        $data = DB::table('propuestas')->where('reg',$data['id'])->where('prefijo', $data['prefijo'])->get();
        
        
        if (count($data) > 0) {

        $lineasdata = DB::table('lineas_propuestas')->where('id_propuesta',$data[0]->reg)->where('prefijo',$data[0]->prefijo)->get();
        $barriospropuesta = DB::table('barrios_propuestas')->where('id_propuesta',$data[0]->reg)->where('prefijo',$data[0]->prefijo)->get();
        
            $pdf = PDF::loadView('pdf-propuesta.index', compact('data','lineasdata','barriospropuesta'));
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

        return view('cotizadoronline.index', compact('data'));
        
    }

    

    public function consultapoliza()
    {
        $data = request()->all();
        $data = DB::table('lineas_propuestas')->where('fechaHasta','>=', '2021-07-15 00:00:00')->where('tipo_documento', $data['tipodocumento'])->where('documento', $data['documento'])->get();
        $success = false;        
        if (count($data) > 0) {
            $success = true;
            //return response()->json($data, 200);
            return view('polizas.index', compact('data','success'));
        } else {
            return view('polizas.index', compact('data','success'));
        }
    }

    public function consultaparametros()
    {
        $req = request()->all();
        $datos = [];

        if($req["rolpuntodeventa"] == "COLABORADOR"){

            $datos["actividades"] = DB::table('actividades')->where('ultmod','>=',$req["fecha_actualizacion_desde"])->where('ultmod','<=',$req["fecha_actualizacion_hasta"])->get();
            $datos["coberturas"] = DB::table('coberturas')->where('ultmod','>=',$req["fecha_actualizacion_desde"])->where('ultmod','<=',$req["fecha_actualizacion_hasta"])->get();
            $datos["clasificaciones"] = DB::table('clasificaciones')->where('ultmod','>=',$req["fecha_actualizacion_desde"])->where('ultmod','<=',$req["fecha_actualizacion_hasta"])->get();

        }

        if($req["rolpuntodeventa"] == "PRINCIPAL"){
            $datos["propuestas"] = DB::table('propuestas')->where('ultmod','>=',$req["fecha_actualizacion_desde"])->where('ultmod','<=',$req["fecha_actualizacion_hasta"])->where('prefijo','=','O')->get();
            
            $datos["lineas_propuestas"] = DB::table('lineas_propuestas')->where('ultmod','>=',$req["fecha_actualizacion_desde"])->where('ultmod','<=',$req["fecha_actualizacion_hasta"])->where('prefijo','=','O')->get();
        }


        

        return response()->json($datos, 200);
        
    }
}
