<?php

namespace App;

use App\Taxpayer;
use App\ChartVersion;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TaxpayerScope;

class Cycle extends Model
{

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TaxpayerScope);
    }

    public function scopeMy($query, Taxpayer $taxPayer, $date)
    {
    
        $query->withoutGlobalScopes()
        ->where('taxpayer_id', $taxPayer->id)
        ->where('start_date', '<=', $date)
        ->where('end_date', '>=', $date);

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
     * Get the chartVersion that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chartVersion()
    {
        return $this->belongsTo(ChartVersion::class);
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

    /**
     * Get the cycleBudgets for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cycleBudgets()
    {
        return $this->hasMany(CycleBudget::class);
    }
}
