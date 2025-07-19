<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPick extends Model
{
    protected $table = 'stock_picks';

    protected $fillable = [
        'symbol',
        'newsletter_id',
        'recommendation_date',
        'initial_price',
        'current_price'
    ];

    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class);
    }
}
