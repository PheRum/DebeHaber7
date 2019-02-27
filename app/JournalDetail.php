<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model {
    protected $fillable = [
        'type',
        'journal_id',
        'chart_id',
        'debit',
        'credit',
    ];

    public function getKeyName() {
        return 'id';
    }
    /**
    * Get the journal that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function journal() {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }

    /**
    * Get the chart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function chart() {
        return $this->belongsTo(Chart::class,'chart_id','id');
    }
}
