<?php

namespace Database\Factories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{

    protected $model = Tax::class;

    public function definition()
    {

        return [
            'name_ar' => $this->faker->word,
            'name_en' =>$this->faker->word ,
            'tax' =>$this->faker->numberBetween(5,15) ,
        ];
    }
}
