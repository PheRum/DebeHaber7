<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TaxpayerScope implements Scope
{
    /**
    * Global Scope that works as follows:
    * Bring all Journal from Taxpayer.
    *
    * @param  \Illuminate\Database\Eloquent\Builder  $builder
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('taxpayer_id', request()->route('taxPayer')->id);
    }
}
