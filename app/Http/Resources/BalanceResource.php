<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'sub_type' => $this->sub_type,
            'is_accountable' => $this->is_accountable,
            'debit'=>$this->debit,
            'credit'=>$this->credit,
            'detail_id'=>$this->detail_id,
            'comment' =>$this->comment
        ];
    }
}
