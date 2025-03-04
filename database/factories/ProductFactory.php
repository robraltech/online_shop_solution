<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SubCategory; // ✅ Import SubCategory model

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        return [
            "title" => $title,
            "slug" => $slug,
            "category_id" => 28,
            "description" => fake()->text(200),
            "compare_price" => fake()->randomFloat(2, 10, 1000),
            "sub_category_id" => SubCategory::inRandomOrder()->first()->id ?? 1, // ✅ Pick an existing ID
            "brand_id" => rand(1, 6),
            "price" => fake()->randomFloat(2, 10, 1000),
            "sku" => rand(1000, 100000),
            "track_qty" => "Yes",
            "qty" => 10,
            "is_featured" => "Yes",
            "status" => 1
        ];
    }
}
