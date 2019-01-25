<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChartVersion extends Model
{

    public function scopeMy($query, $taxPayer)
    {
        return $query
        ->where('taxpayer_id', $taxPayer->id)
        ->orWhere(function ($subQuery) use ($taxPayer) {
            $subQuery
            //->where('country', $taxPayer->country)
            ->whereNull('taxpayer_id');
        });
    }
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
     * Get the charts for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function charts()
    {
        return $this->hasMany(Chart::class);
    }

    /**
     * Get the cycles for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cycles()
    {
        return $this->hasMany(Cycle::class);
    }
}
