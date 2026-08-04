<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class gruposbarrio extends Model
{
    use HasFactory;

    protected $table = 'gruposbarrios';

    protected $primaryKey = 'reg';

    protected $fillable = [
        'id',
        'nombre',
        'idbarrio',
        'nombrebarrio',
        'ultmod',
        'codestado',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public const STATUS_ACTIVE = 1;

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(barrio::class, 'idbarrio', 'id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('codestado', self::STATUS_ACTIVE);
    }
}
