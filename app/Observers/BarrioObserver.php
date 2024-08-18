<?php

namespace App\Observers;

use App\Models\barrio;
use App\Models\Cola;

class BarrioObserver
{
    /**
     * Handle the barrio "created" event.
     *
     * @param  \App\Models\barrio  $barrio
     * @return void
     */
    public function created(barrio $barrio)
    {
        Cola::create([
            'entity' => 'barrios',
            'entity_id' => $barrio->reg,
            'codempresa' => $barrio->codempresa,
        ]);
    }

    /**
     * Handle the barrio "updated" event.
     *
     * @param  \App\Models\barrio  $barrio
     * @return void
     */
    public function updated(barrio $barrio)
    {
        Cola::create([
            'entity' => 'barrios',
            'entity_id' => $barrio->reg,
            'codempresa' => $barrio->codempresa,
        ]);
    }

    /**
     * Handle the barrio "deleted" event.
     *
     * @param  \App\Models\barrio  $barrio
     * @return void
     */
    public function deleted(barrio $barrio)
    {
        //
    }

    /**
     * Handle the barrio "restored" event.
     *
     * @param  \App\Models\barrio  $barrio
     * @return void
     */
    public function restored(barrio $barrio)
    {
        //
    }

    /**
     * Handle the barrio "force deleted" event.
     *
     * @param  \App\Models\barrio  $barrio
     * @return void
     */
    public function forceDeleted(barrio $barrio)
    {
        //
    }
}
