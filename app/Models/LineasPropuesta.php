<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LineasPropuesta extends Model
{
    use HasFactory;

    protected $table = 'lineas_propuestas';

    protected $fillable = [
        'reg',
        'id_propuesta',
        'documento',
        'tipo_documento',
        'apellidos',
        'nombres',
        'fecha_nacimiento',
        'id_actividad',
        'id_clasificacion',
        'premio',
        'ultmod',
        'user_edit',
        'codestado',
        'prefijo',
        'actividad',
        'clasificacion',
        'fechaDesde',
        'fechaHasta',
        'codempresa',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function propuesta(): BelongsTo
    {
        return $this->belongsTo(Propuesta::class, 'id_propuesta', 'idpropuesta');
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividade::class, 'id_actividad', 'reg');
    }

    public function clasificacion(): BelongsTo
    {
        return $this->belongsTo(Clasificacione::class, 'id_clasificacion', 'reg');
    }
}
