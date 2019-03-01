<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RyanWeber\Mutators\Timezoned;
use Laravel\Scout\Searchable;

class Transaction extends Model
{
    use SoftDeletes, Searchable;
    use Timezoned;

    protected $timezoned = ['date', 'created_at', 'updated_at', 'deleted_at'];
    protected $softDelete = true;
    protected $fillable = [
        'type',
        'taxpayer_id',
        'partner_name',
        'partner_taxid',
        'document_id',
        'currency_id',
        'rate',
        'payment_condition',
        'chart_account_id',
        'date',
        'number',
        'code',
        'code_expiry',
        'comment',
    ];

    public function toSearchableArray()
    {

        return [
            'type' => $this->type,
            'taxpayer_id' => $this->taxpayer_id,
            'partner_name' => $this->partner_name,
            'partner_taxid' => $this->partner_taxid,
            'currency' => $this->currency,
            //'items' => $this->items->flatMap->name,
            'payment_condition' => $this->payment_condition,
            'date' => $this->date,
            'number' => $this->number,
            'code' => $this->code,
            'comment' => $this->comment,
        ];
    }

    public function scopeMy($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');
        return $query->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMySales($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');
        return $query->where('type', 3)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMySalesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 3)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMyCreditNotes($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->where('type', 4)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMyCreditNotesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 4)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMyPurchases($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->where('type', 1)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMyPurchasesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 1)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMyDebitNotes($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->where('type', 2)
            ->where('taxpayer_id', $taxPayerID);
    }

    public function scopeMyDebitNotesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 2)
            ->where('taxpayer_id', $taxPayerID);
    }

    /**
    * Get the accountChart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function journal()
    {
        return $this->belongsTo(\App\Journal::class);
    }

    /**
    * Get the accountChart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function accountChart()
    {
        return $this->belongsTo(Chart::class, 'chart_account_id', 'id');
    }

    /**
    * Get the taxPayer that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function customer()
    {
        return $this->belongsTo(Taxpayer::class, 'customer_id');
    }

    /**
    * Get the taxPayer that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function supplier()
    {
        return $this->belongsTo(Taxpayer::class, 'supplier_id');
    }

    /**
    * Get the document that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function document()
    {
        return $this->belongsTo(Document::class);
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
    * Get the impex that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function impex()
    {
        return $this->belongsTo(Impex::class);
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
    * Get the details for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
