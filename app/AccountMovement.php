<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\TaxpayerScope;

class AccountMovement extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id'
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TaxpayerScope);
    }

    public function scopeMy($query, $startDate, $endDate, $taxPayerID)
    {
        return $query->where('taxpayer_id', $taxPayerID)
            ->whereBetween('date', [$startDate, $endDate])
            ->withoutGlobalScopes()
            ->with('transaction');
    }

    public function scopePaymentsMade($query, $startDate, $endDate, $taxPayerID)
    {
        return $query->where('taxpayer_id', $taxPayerID)
            ->whereBetween('date', [$startDate, $endDate])
            ->withoutGlobalScopes()
            ->with('transaction')
            ->whereHas('transaction', function ($q) use ($taxPayerID) {
                $q->where('taxpayer_id', '=', $taxPayerID)
                    ->where('payment_condition', '>', 0)
                    ->where('type', 1);
            })->get();;
    }

    public function scopePaymentsRecieved($query, $startDate, $endDate, $taxPayerID)
    {
        return $query->where('taxpayer_id', $taxPayerID)
            ->whereBetween('date', [$startDate, $endDate])
            ->withoutGlobalScopes()
            ->with('transaction')
            ->whereHas('transaction', function ($q) use ($taxPayerID) {
                $q->where('taxpayer_id', '=', $taxPayerID)
                    ->where('payment_condition', '>', 0)
                    ->where('type', 3);
            })->get();;
    }

    /**
    * Get the transaction that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
    * Get the chart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function chart()
    {
        return $this->belongsTo(Chart::class);
        //->withoutGlobalScopes();
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
    * Get the taxPayer that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function taxPayer()
    {
        return $this->belongsTo(Taxpayer::class);
    }
}
