<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalSimDetail extends Model
{
    //

    /**
     * Get the journalSimulation that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journalSimulation()
    {
        return $this->belongsTo(JournalSim::class);
    }

    /**
     * Get the chart that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chart()
    {
        return $this->belongsTo(Chart::class);
    }
}
