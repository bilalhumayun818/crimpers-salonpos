<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class OutOfStockProductsSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 13; $i++) {
            Product::create([
                'name' => 'Test Out of Stock Product ' . $i,
                'sku' => 'TEST-OOS-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'description' => 'Test product created by seeder to test scrolling',
                'selling_price' => 500,
                'cost_price' => 300,
                'current_stock' => 0,
                'min_stock_level' => 5,
                'product_type' => 'retail',
                'track_inventory' => true,
            ]);
        }
    }
}
