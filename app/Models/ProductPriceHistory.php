<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'old_selling_price',
        'new_selling_price',
        'old_cost_price',
        'new_cost_price',
    ];

    protected $casts = [
        'old_selling_price' => 'decimal:2',
        'new_selling_price' => 'decimal:2',
        'old_cost_price'    => 'decimal:2',
        'new_cost_price'    => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
