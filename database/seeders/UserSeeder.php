<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    public function run()
    {
        $user=User::create([
            'name' => 'admin',
            'role' => 'admin',
            'activation' => 1,
            'add_product' => 1,
            'store_name' => 'lady_spa',
            'location' => 'riyadh',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'phone' => "0424",
            'geoLocation' => '1,1',
            'whatsapp' => "21312",
        ]);
    }
}
