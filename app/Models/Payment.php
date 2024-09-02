<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Table name
    protected $table = 'payment';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    // Fillable
    protected $fillable = ['user_id', 'order_id', 'coupon_id', 'ip_address','gateway_response','plan_id','amount','reference','status_response','due_date','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
