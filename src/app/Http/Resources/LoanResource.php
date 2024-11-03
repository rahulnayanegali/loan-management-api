<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'interest_rate' => $this->interest_rate,
            'duration_months' => $this->duration_months,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
