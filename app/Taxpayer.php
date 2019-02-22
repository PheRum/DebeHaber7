<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Taxpayer extends Model
{
    use Searchable;

    protected $fillable = [
        'name',
        'country',
        'alias',
        'taxid',
        'address',
        'email',
        'telephone'
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'taxid' => $this->taxid,
            'alias' => $this->alias,
            'email' => $this->email
        ];
    }

    public function setting()
    {
        return $this->hasOne(\App\TaxpayerSetting::class, 'taxpayer_id');
    }

    /**
    * Get the integrations for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function integrations()
    {
        return $this->hasMany(TaxpayerIntegration::class);
    }

    /**
    * Get the integrations for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function currencies()
    {
        return $this->hasMany(TaxpayerCurrency::class);
    }

    /**
    * Get the journalSims for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function journalSims()
    {
        return $this->hasMany(JournalSim::class);
    }

    /**
    * Get the journalTemplates for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function journalTemplates()
    {
        return $this->hasMany(JournalTemplate::class);
    }

    /**
    * Get the chartVersions for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function chartVersions()
    {
        return $this->hasMany(ChartVersion::class);
    }

    /**
    * Get the impexes for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function impexes()
    {
        return $this->hasMany(Impex::class);
    }

    /**
    * Get the taxPayerTypes for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function taxPayerTypes()
    {
        return $this->hasMany(TaxpayerType::class);
    }

    /**
    * Get the taxPayerFavs for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function taxPayerFavs()
    {
        return $this->hasMany(TaxpayerFav::class);
    }

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
    * Get the documents for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function documents()
    {
        return $this->hasMany(Document::class);
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
    * Get the productions for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function productions()
    {
        return $this->hasMany(Production::class);
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
    * Get the journals for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function journals()
    {
        return $this->hasMany(Journal::class);
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
