<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalTemplate extends Model
{
    //

    /**
     * Get the taxPayer that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxPayer()
    {
        return $this->belongsTo(Taxpayer::class);
    }

    /**
     * Get the details for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(JournalTemplateDetail::class);
    }
}
