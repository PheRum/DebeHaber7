<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalTransaction extends Model
{
    //
    protected $fillable = [
        'type',
        'taxpayer_id',
        'transaction_id'
    ];

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
     * Get the Journal that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Journal()
    {
        return $this->belongsTo(Journal::class);
    }
}
