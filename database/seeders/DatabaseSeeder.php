<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DeliveryServiceProvider;
use App\Models\Color;
use App\Models\Customer;
use App\Models\Images;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Size;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(UserSeeder::class);
        Setting::create(
            [
                "contact_land_line" => '0236760108',
                "contact_whatsapp" => '01025070424',
                "contact_phone" => '01025070424',
                "contact_email" => 'khalednasser546@gmail.com',
            ]
        );
    }
}
