<?php

namespace App;

use Laravel\Spark\Team as SparkTeam;

class Team extends SparkTeam
{
    //

    /**
     * Get the taxPayerIntegrations for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxPayerIntegrations()
    {
        return $this->hasMany(TaxpayerIntegration::class);
    }
}
