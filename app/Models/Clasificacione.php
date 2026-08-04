<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clasificacione extends Model
{
    use HasFactory;

    protected $table = 'clasificaciones';

    protected $fillable = [
        'reg',
        'cod',
        'nombre',
        'id_actividad',
        'ultmod',
        'user_edit',
        'codestado',
        'version',
    ];

    protected $casts = [
        'cod' => 'integer',
        'id_actividad' => 'integer',
        'version' => 'integer',
    ];

    public const STATUS_ACTIVE = '1';

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividade::class, 'id_actividad', 'id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('codestado', self::STATUS_ACTIVE);
    }

    public function scopeForActivity(Builder $query, int $activityId): Builder
    {
        return $query->where('id_actividad', $activityId);
    }
}
