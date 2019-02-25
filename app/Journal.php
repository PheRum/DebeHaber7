<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\BinaryUuid\HasBinaryUuid;
use App\Scopes\JournalScope;
use App\Cycle;
use App\JournalDetail;
use App\Taxpayer;
use Carbon\Carbon;

class Journal extends Model
{
    use HasBinaryUuid;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new JournalScope);
    }

    protected $fillable = [
        'cycle_id',
        'number',
        'date',
        'comment',
        'is_accountable',
        'is_presented',
        'is_first',
        'is_last',
    ];

    public function getKeyName()
    {
        return 'id';
    }

    /**
    * Get the details for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function details()
    {
        return $this->hasMany(JournalDetail::class, 'journal_id', 'id')->orderBy('credit', 'desc');
    }

    public function transactions()
    {
        return $this->hasManyThrough(\App\Transaction::class, \App\JournalTransaction::class);
    }

    /**
    * Get the cycle that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    /**
    * Get the productions for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function productions()
    {
        return $this->hasMany(JournalProduction::class);
    }

    /**
    * Get the accountMovements for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function accountMovements()
    {
        return $this->hasMany(JournalAccountMovement::class);
    }

    /**
    * Get only the total value from details.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function total()
    {
        return $this->hasMany(JournalDetail::class)
        ->selectRaw('sum(credit) as total');
    }

    public function scopeEntries($query, $startDate, $endDate, $cycleID)
    {
        return $query->join('journal_details', 'journals.id', 'journal_details.journal_id')
        ->join('charts', 'charts.id', 'journal_details.chart_id')
        ->select(
            'journals.id',
            'journals.date',
            'journals.comment',
            'journals.number',
            'journal_details.debit',
            'journal_details.credit',
            'charts.id as chart_id',
            'charts.name as chartName',
            'charts.code as chartCode',
            'charts.type as chartType',
            'charts.sub_type as chartSubType'
        )
        ->whereBetween('journals.date', [$startDate, $endDate])
        ->where('cycle_id', $cycleID);
    }
}
