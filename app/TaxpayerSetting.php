<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxpayerSetting extends Model
{
    protected $fillable = [
        'taxpayer_id',
        'type',
        'regime_type',
        'agent_name',
        'agent_taxid',
        'show_inventory',
        'show_production',
        'show_fixedasset',
        'is_company'
    ];

    public $timestamps  = false;

    public function taxpayer()
    {
        return $this->belongsTo(\App\Taxpayer::class, 'taxpayer_id');
    }
}
