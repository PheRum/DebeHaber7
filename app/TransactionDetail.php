<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'type',
        'transaction_id',
        'chart_id',
        'chart_vat_id',
        'value',
        'rate'
    ];

    public function scopeVAT($query)
    { }

    /**
    * Get the transaction that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
    * Get the vatChart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function vat()
    {
        return $this->belongsTo(Chart::class, 'chart_vat_id', 'id');
    }

    /**
    * Get the chart for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function chart()
    {
        return $this->belongsTo(Chart::class, 'id', 'chart_id');
    }

    /**
     * Get the impexExpense that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function impexExpense()
    {
        return $this->belongsTo(ImpexExpense::class);
    }
}
