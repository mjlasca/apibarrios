<?php

namespace App\Observers;

use App\Models\Cola;
use App\Models\Propuesta;

class ProposalObserver
{

    /**
     * Handle the Propuesta "created" event.
     *
     * @param  \App\Models\Propuesta  $propuesta
     * @return void
     */
    public function created(Propuesta $proposal)
    {
        
    }

    /**
     * Handle the Propuesta "updated" event.
     *
     * @param  \App\Models\Propuesta  $propuesta
     * @return void
     */
    public function updated(Propuesta $proposal)
    {
        
    }

    /**
     * Handle the Propuesta "deleted" event.
     *
     * @param  \App\Models\Propuesta  $propuesta
     * @return void
     */
    public function deleted(Propuesta $propuesta)
    {
        //
    }

    /**
     * Handle the Propuesta "restored" event.
     *
     * @param  \App\Models\Propuesta  $propuesta
     * @return void
     */
    public function restored(Propuesta $propuesta)
    {
        //
    }

    /**
     * Handle the Propuesta "force deleted" event.
     *
     * @param  \App\Models\Propuesta  $propuesta
     * @return void
     */
    public function forceDeleted(Propuesta $propuesta)
    {
        //
    }
}
