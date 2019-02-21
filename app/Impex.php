<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impex extends Model
{
    //
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
     * Get the expenses for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(ImpexExpense::class);
    }
}
