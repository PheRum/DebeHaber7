<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
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
}
