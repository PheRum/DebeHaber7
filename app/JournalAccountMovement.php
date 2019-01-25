<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalAccountMovement extends Model
{


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
     * Get the accountMovement that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountMovement()
    {
        return $this->belongsTo(AccountMovement::class);
    }
}
