<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $getPlan = DB::table('plan')->select('plan_name','plan_type', 'amount')->where('track', '=', $this->plan_id)->first();
        return [
            'id' => $this->id,
            'user' => $this->user,
            'order_id' => $this->order_id,
            'coupon_id' => $this->coupon_id,
            'amount' => $this->amount,
            'plan' => $getPlan,
            'status' => $this->status,
            'reference' => $this->reference,
            'status_response' => $this->status_response,
            'due_date' => $this->due_date,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
