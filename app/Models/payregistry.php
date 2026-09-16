<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payregistry extends Model
{
    use HasFactory;

    protected $fillable = [
        'idpropuesta',
        'prefijo',
        'usuariopaga',
        'fecha_paga',
        'tipopago',
        'compformadepago',
        'fecha_comprobante',
        'valor_pagado',
        'cuit_pagador'
    ];
}
