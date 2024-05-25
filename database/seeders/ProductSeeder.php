<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::factory()->count(100)->create();
        foreach ($products as $product) {
            // Get random product IDs excluding the current product
            $relatedProductIds = $products->where('id', '!=', $product->id)->random(3)->pluck('id')->toArray();

            // Update the related_products field with comma-separated IDs
            $product->related_products = implode(',', $relatedProductIds);
            $product->save();
        }
    }
}
