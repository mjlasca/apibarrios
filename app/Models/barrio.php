<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class barrio extends Model
{
    use HasFactory;

    protected $table = 'barrios';

    protected $primaryKey = 'reg';

    protected $fillable = [
        'id',
        'nombre',
        'telefono',
        'direccion',
        'email',
        'sub_barrio',
        'clase_barrio',
        'suma_muerte',
        'suma_gm',
        'suma_rc',
        'exige',
        'observaciones',
        'ultmod',
        'user_edit',
        'codestado',
    ];

    protected $casts = [
        'id' => 'string',
        'suma_muerte' => 'float',
    ];

    public const STATUS_ACTIVE = '1';

    public function gruposbarrios(): HasMany
    {
        return $this->hasMany(gruposbarrio::class, 'idbarrio', 'id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('codestado', self::STATUS_ACTIVE);
    }

    public function scopeWithSumaMuerte(Builder $query): Builder
    {
        return $query->whereNotNull('suma_muerte')->where('suma_muerte', '!=', '');
    }
}
