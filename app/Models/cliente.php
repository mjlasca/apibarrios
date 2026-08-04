<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'reg';

    protected $fillable = [
        'id',
        'nombres',
        'apellidos',
        'tipo_id',
        'telefono',
        'direccion',
        'email',
        'codpostal',
        'localidad',
        'ciudad',
        'sexo',
        'fecha_nacimiento',
        'situacion',
        'ultmod',
        'user_edit',
        'codestado',
        'categoria',
        'codempresa',
        'puntodeventa',
        'idaseguradora',
        'cuir',
    ];

    protected $casts = [
        'id' => 'string',
        'fecha_nacimiento' => 'date',
    ];

    public const STATUS_ACTIVE = '1';

    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class, 'documento', 'id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('codestado', self::STATUS_ACTIVE);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->nombres.' '.$this->apellidos);
    }
}
