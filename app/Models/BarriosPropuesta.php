<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Legacy pivot table; the project currently persists neighborhoods
 * inside Propuesta::$data_barrios, so this model is kept only for
 * backwards compatibility with older consumers.
 */
class BarriosPropuesta extends Model
{
    use HasFactory;

    protected $table = 'barrios_propuestas';

    protected $fillable = [
        'reg',
        'id_propuesta',
        'id_barrio',
        'nombre',
        'ultmod',
        'user_edit',
        'codestado',
        'prefijo',
    ];
}
