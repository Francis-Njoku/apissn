<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    // Table name
    protected $table = 'plan';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    protected $fillable = ['plan_name', 'plan_type', 'amount', 'track','status'];
}
