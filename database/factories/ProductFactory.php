<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Faker\Factory as FakerFactory;
use App\Faker\ProductNameProvider;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition(): array
    {
        // Create a Faker instance and add the custom provider
        $faker = FakerFactory::create();
        $faker->addProvider(new ProductNameProvider($faker));

        // Ensure there are categories, subcategories, and brands in the database
        $category = Category::inRandomOrder()->first();
        $subCategory = SubCategory::where('category_id', $category->id)->inRandomOrder()->first();
        $brand = Brand::inRandomOrder()->first();

        $title = $faker->productName;
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $this->faker->realText(2000),
            'short_description' => $this->faker->realText(100),
            'shipping_returns' => $this->faker->realText(100),
            'related_products' => '',
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'compare_price' => $this->faker->randomFloat(2, 10, 1000),
            'category_id' => $category->id,
            'sub_category_id' => $subCategory ? $subCategory->id : null,
            'brand_id' => $brand ? $brand->id : null,
            'is_featured' => $this->faker->randomElement(['Yes', 'No']),
            'sku' => strtoupper(Str::random(10)),
            'barcode' => $this->faker->ean13,
            'track_qty' => $this->faker->randomElement(['Yes', 'No']),
            'qty' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}
