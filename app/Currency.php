<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    protected $table = 'currencies';
    protected $fillable = [
      'id',
      'name',
      'code'

    ];

    /**
     * Get the taxPayerCurrencies for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxPayerCurrencies()
    {
        return $this->hasMany(TaxpayerCurrency::class);
    }

    /**
     * Get the fixedAssets for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fixedAssets()
    {
        return $this->hasMany(FixedAsset::class);
    }

    /**
     * Get the transactions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the accountMovements for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountMovements()
    {
        return $this->hasMany(AccountMovement::class);
    }

    /**
     * Get the productions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    /**
     * Get the currencyRates for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currencyRates()
    {
        return $this->hasMany(CurrencyRates::class);
    }
}
