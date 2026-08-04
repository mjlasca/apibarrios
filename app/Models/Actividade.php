<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividade extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'reg',
        'cod',
        'nombre',
        'ultmod',
        'user_edit',
        'codestado',
        'version',
    ];

    protected $casts = [
        'cod' => 'integer',
        'version' => 'integer',
    ];

    public const STATUS_ACTIVE = '1';

    public function clasificaciones(): HasMany
    {
        return $this->hasMany(Clasificacione::class, 'id_actividad', 'id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('codestado', self::STATUS_ACTIVE);
    }
}
