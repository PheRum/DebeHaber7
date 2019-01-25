<?php

namespace App;

use Laravel\Spark\Team as SparkTeam;

class Team extends SparkTeam
{
  //

  public function taxPayerIntegration()
  {
    return $this->hasMany(App\TaxpayerIntegration::class);
  }
}
