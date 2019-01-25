<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChartAlias extends Model
{
    //

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
