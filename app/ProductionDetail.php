<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionDetail extends Model
{
    //
    protected $fillable = [
        'type',
        'production_id',
        'is_input',
        'chart_id',
        'value',
        'comment',
    ];

    /**
    * Get the production that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function production()
    {
        return $this->belongsTo(Production::class, 'foreign_key', 'local_key');
    }

    /**
    * Get the chart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function chart()
    {
        return $this->belongsTo(Chart::class, 'foreign_key', 'local_key');
    }
}
