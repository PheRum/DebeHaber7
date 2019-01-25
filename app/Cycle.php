<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{

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
     * Get the journalSimulation for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalSimulation()
    {
        return $this->hasMany(JournalSim::class);
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
