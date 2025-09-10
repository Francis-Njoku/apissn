<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\UserGroup;
use App\Http\Resources\UserGroupResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //$usergroup = DB::UserGroup();

        //$getUser = DB::table('users')->select('firstName','lastName','email')->where('id', '=', $this->manager_id)->get();
        //UserGroup::where('user_id', $this->id)->get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'identity' => $this->identity,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
            'subscriber_status' => $this->subscriber_status,
            'last_payment_date' => $this->last_payment_date ? (new \DateTime($this->last_payment_date))->format('Y-m-d H:i:s') : null,
        ];
    }
}
