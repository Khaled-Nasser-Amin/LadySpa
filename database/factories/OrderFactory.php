<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $subtotal=$this->faker->randomNumber(3);
        $shipping=$this->faker->randomNumber(2);
        $taxes=$this->faker->randomNumber(2);
        $total_amount=$subtotal+$shipping+$taxes;
        $date=$this->faker->dateTimeBetween($startDate = '2021-08-01', $endDate = 'now', $timezone = null);
        return [
            "total_amount" => $total_amount,
            "subtotal" => $subtotal,
            "user_id" => $this->faker->numberBetween(1,10),
            "delivery_service_provider_id" => $this->faker->numberBetween(1,10),
            "shipping" => $shipping,
            "taxes" => $taxes,
            "location" => $this->faker->city,
            "payment_way" => "cash on delivery",
            "receiver_phone" => $this->faker->randomNumber(9),
            "lat_long" => "30.0012797,31.159573",
            "receiver_first_name" => $this->faker->name,
            "receiver_last_name" => $this->faker->name,
            "created_at" => $date,
            "updated_at" => $date,
        ];
    }
}
