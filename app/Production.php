<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    //
    protected $fillable = [
        'type',
        'taxpayer_id',
        'currency_id',
        'rate',
        'name',
        'date',
        'ref_id',
    ];

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
     * Get the currency that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the details for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(ProductionDetail::class);
    }

    /**
     * Get the journals for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
