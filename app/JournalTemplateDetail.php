<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalTemplateDetail extends Model
{
    //

    /**
     * Get the journalTemplate that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journalTemplate()
    {
        return $this->belongsTo(JournalTemplate::class);
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
