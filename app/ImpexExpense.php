<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImpexExpense extends Model
{
    //
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
     * Get the transaction that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class);
    }
}
