<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalProduction extends Model
{
    //

    /**
     * Get the journal that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the production that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}
