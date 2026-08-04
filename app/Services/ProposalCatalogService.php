<?php

namespace App\Services;

use App\Models\Actividade;
use App\Models\barrio;
use App\Models\Clasificacione;
use App\Models\Cobertura;
use App\Models\gruposbarrio;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Read-only catalog of the dropdown data used by the emission form.
 * Results are cached because they mutate rarely and are read often.
 */
class ProposalCatalogService
{
    private const CACHE_TTL = 3600;

    public function activities(): Collection
    {
        return Cache::remember('catalog.activities', self::CACHE_TTL, fn () => Actividade::query()
            ->active()
            ->orderBy('nombre')
            ->get(['id', 'reg', 'cod', 'nombre']));
    }

    public function coverages(): Collection
    {
        return Cache::remember('catalog.coverages', self::CACHE_TTL, fn () => Cobertura::query()
            ->active()
            ->orderBy('suma')
            ->get(['id', 'nombre', 'suma', 'gastos', 'deducible', 'vrMensual', 'vrTrimestral', 'vrSemestral', 'x21', 'x32', 'x64']));
    }

    public function neighborhoods(): Collection
    {
        return Cache::remember('catalog.neighborhoods', self::CACHE_TTL, fn () => barrio::query()
            ->active()
            ->withSumaMuerte()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'email', 'suma_muerte', 'suma_gm']));
    }

    public function neighborhoodGroups(): Collection
    {
        return Cache::remember('catalog.neighborhoodGroups', self::CACHE_TTL, fn () => gruposbarrio::query()
            ->active()
            ->whereNotNull('idbarrio')
            ->where('idbarrio', '!=', '')
            ->groupBy('id', 'nombre')
            ->orderBy('nombre')
            ->get(['id', 'nombre']));
    }

    public function classificationsForActivity(int $activityId): Collection
    {
        return Clasificacione::query()
            ->active()
            ->forActivity($activityId)
            ->orderBy('nombre')
            ->get(['id', 'reg', 'cod', 'nombre']);
    }
}
