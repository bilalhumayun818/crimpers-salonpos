<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class Product extends Model
{
    use BelongsToBranch;
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'description',
        'selling_price',
        'cost_price',
        'current_stock',
        'min_stock_level',
        'product_type',
        'sku',
        'track_inventory',
        'last_restocked'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock_level' => 'integer',
        'last_restocked' => 'date',
        'track_inventory' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoiceItems()
    {
        return $this->morphMany(InvoiceItem::class, 'itemizable');
    }

    public function productUsages()
    {
        return $this->hasMany(ProductUsage::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(ProductPriceHistory::class)->latest();
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function isLowStock()
    {
        return $this->track_inventory && $this->current_stock <= $this->min_stock_level;
    }

    public function isOutOfStock()
    {
        return $this->track_inventory && $this->current_stock <= 0;
    }

    public function getStockStatusAttribute()
    {
        if (!$this->track_inventory) return 'not_tracked';

        if ($this->current_stock <= 0) return 'out_of_stock';
        if ($this->current_stock <= $this->min_stock_level) return 'low_stock';

        return 'in_stock';
    }

    public function deductStock($quantity)
    {
        if ($this->track_inventory) {
            $this->decrement('current_stock', $quantity);
        }
    }

    public function addStock($quantity)
    {
        if ($this->track_inventory) {
            $this->increment('current_stock', $quantity);
            $this->update(['last_restocked' => now()]);
        }
    }
}
