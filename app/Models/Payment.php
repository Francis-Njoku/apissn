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

    public function scopeSuccessful($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'success')
            ->orWhere('status_response', 'success')
            ->orWhere('gateway_response', 'successful');
        });
    }

    /**
     * Choose which date column to filter on (defaults to created_at).
     */
    public function scopeUsingDateField($query, string $field = 'created_at')
    {
        // Fallback to created_at if the column doesn’t exist
        $allowed = ['created_at', 'updated_at', 'due_date'];
        $field = in_array($field, $allowed, true) ? $field : 'created_at';
        return $query->whereNotNull($field)->select('*')->addSelect(\DB::raw("{$field} as filter_date"));
    }

}
