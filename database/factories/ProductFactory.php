<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    protected $model = Product::class;

    public function definition()
    {

        $name_en=$this->faker->word;
        $name_ar=$this->faker->word;
        return [
            'name_ar'=>$name_ar,
            'name_en'=>ucfirst($name_en),
            'slug'=>$name_en."-".$name_ar,
            'description_ar'=>$this->faker->text(200),
            'description_en'=>$this->faker->text(200),
            'typeOfFabric'=>$this->faker->word,
            'typeOfSleeve'=>$this->faker->word,
            'additions'=>$this->faker->word,
            'isActive'=>$this->faker->numberBetween(0,1),
            'user_id'=>$this->faker->numberBetween(1,10),
            'category_id'=>$this->faker->numberBetween(1,10),

            'image'=>$this->faker->numberBetween(1,10).".jpg",
        ];

    }
}
