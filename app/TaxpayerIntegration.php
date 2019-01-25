<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxpayerIntegration extends Model
{

    protected $fillable = [
      'id'
    ];
    
    public function scopeMyTaxPayers($query, $teamID)
    {
        return $query->where('team_id', $teamID);
    }

    /**
     * Get the taxPayer that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxpayer()
    {
        return $this->belongsTo(Taxpayer::class);
    }

    /**
     * Get the team that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
