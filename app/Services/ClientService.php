<?php

namespace App\Services;

use App\Models\cliente;
use Illuminate\Support\Collection;

class ClientService
{
    /**
     * Search active clients matching the document id or full name.
     * Minimum query length is enforced by the caller (controller).
     * Duplicated documents are collapsed into their most recent record
     * (MAX reg) so the autocomplete always fills the freshest data.
     */
    public function search(string $query): Collection
    {
        $terms = array_filter(explode(' ', trim($query)));

        $latestIds = cliente::query()
            ->active()
            ->where(function ($builder) use ($query, $terms) {
                $builder->where('id', 'like', "%{$query}%");

                foreach ($terms as $term) {
                    $builder->orWhere('nombres', 'like', "%{$term}%")
                        ->orWhere('apellidos', 'like', "%{$term}%");
                }
            })
            ->groupBy('id')
            ->selectRaw('MAX(reg) as reg')
            ->pluck('reg');

        return cliente::query()
            ->whereIn('reg', $latestIds)
            ->orderBy('id')
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'tipo_id', 'fecha_nacimiento', 'telefono', 'email', 'codestado']);
    }

    /**
     * Resolve a client by its exact document id, always returning the
     * most recent record when duplicates exist (MAX reg).
     */
    public function findByDocument(string $document): ?cliente
    {
        $latestId = cliente::query()
            ->active()
            ->where('id', $document)
            ->groupBy('id')
            ->selectRaw('MAX(reg) as reg')
            ->value('reg');

        return $latestId ? cliente::query()->find($latestId) : null;
    }

    /**
     * Create the client if it does not exist, otherwise update its data.
     * This allows proposals to be saved with typed-in, non-persisted clients.
     */
    public function findOrUpsert(array $data): cliente
    {
        $document = trim($data['documento'] ?? $data['id'] ?? '');

        $client = cliente::query()->where('id', $document)->first();

        $attributes = [
            'id' => $document,
            'nombres' => trim($data['nombres'] ?? ''),
            'apellidos' => trim($data['apellidos'] ?? ''),
            'tipo_id' => trim($data['tipo_id'] ?? 'DNI'),
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'telefono' => trim($data['telefono'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'codestado' => cliente::STATUS_ACTIVE,
            'ultmod' => now(),
            'user_edit' => auth()->user()->name ?? 'online',
        ];

        if ($client) {
            $client->update($attributes);

            return $client->refresh();
        }

        return cliente::query()->create($attributes);
    }
}
