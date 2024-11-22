<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    use HasFactory;

    public static function newMsg($msg,$cod,$idpropuesta = '',$prefijo = '') : void {
        $logs = new logs();
        $logs->saveerror($msg, $idpropuesta, $prefijo, $cod);
    }

    public function saveerror($err,$idpropuesta,$prefijo,$coderror){
        $logs = new logs();
        $logs->message = $err;
        $logs->idpropuesta = $idpropuesta;
        $logs->prefijo = $prefijo;
        $logs->coderror = $coderror;
        $logs->save();
    }
}
