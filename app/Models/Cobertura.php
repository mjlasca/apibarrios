<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobertura extends Model
{
    use HasFactory;

    protected $table = 'coberturas';

    protected $primaryKey = 'reg';

    protected $fillable = [
        'id',
        'nombre',
        'suma',
        'gastos',
        'deducible',
        'vrMensual',
        'vrTrimestral',
        'vrSemestral',
        'x21',
        'x32',
        'x64',
        'ultmod',
        'user_edit',
        'codestado',
        'version',
    ];

    protected $casts = [
        'suma' => 'float',
        'gastos' => 'float',
        'deducible' => 'float',
        'vrMensual' => 'float',
        'vrTrimestral' => 'float',
        'vrSemestral' => 'float',
        'x21' => 'float',
        'x32' => 'float',
        'x64' => 'float',
    ];

    public const STATUS_ACTIVE = '1';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('codestado', self::STATUS_ACTIVE);
    }

    /**
     * Estimate the total prize for a given number of months.
     * Applies promotion pricing (2x1, 3x2, 6x4) when available.
     */
    public function prizeForMonths(int $months): float
    {
        $promo = match ($months) {
            2 => $this->x21 ?: $this->vrMensual * $months,
            3 => $this->x32 ?: $this->vrMensual * $months,
            6 => $this->x64 ?: $this->vrMensual * $months,
            default => $this->vrMensual * $months,
        };

        return (float) $promo;
    }

    public function promotionLabel(int $months): string
    {
        return match ($months) {
            2 => $this->x21 ? '2x1' : '',
            3 => $this->x32 ? '3x2' : '',
            6 => $this->x64 ? '6x4' : '',
            default => '',
        };
    }
}
