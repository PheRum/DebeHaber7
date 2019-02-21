<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChartScope implements Scope
{
    /**
    * Global Scope that works as follows:
    * Bring all Charts from Taxpayer, or where Taxpayer is null and country is same as taxpayer.
    * This will allow to bring charts that are generic but only form same country.
    *
    * Problem: This will not bring from current Cycle Version. Include that into Logic.
    *
    * @param  \Illuminate\Database\Eloquent\Builder  $builder
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function apply(Builder $builder, Model $model)
    {
        $taxPayer = request()->route('taxPayer');
        $cycle = request()->route('cycle');

        if (isset($taxPayer) && isset($cycle))
        {
            //Get Specific for current version.
            $builder->where(function($subQuery) use ($taxPayer, $cycle)
            {
                $subQuery
                ->where('charts.taxpayer_id', $taxPayer->id)
                ->where('charts.chart_version_id', $cycle->chart_version_id);
            })
            //Get Generic for Current Version
            ->orWhere(function($subQuery) use ($taxPayer, $cycle)
            {
                $subQuery
                ->whereNull('charts.taxpayer_id')
                ->where('charts.country', $taxPayer->country)
                ->where('charts.chart_version_id', $cycle->chart_version_id);
            });
        }
    }
}
