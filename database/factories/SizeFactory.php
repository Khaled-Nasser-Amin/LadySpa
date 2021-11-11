<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class SizeFactory extends Factory
{

    protected $model = Size::class;

    public function definition()
    {
        return [
            'size'=>Arr::random(['lg','sm','xl','xxl']),
            'stock'=>$this->faker->numberBetween(1,20),
            'color_id'=>$this->faker->numberBetween(1,150),

        ];
    }
}
