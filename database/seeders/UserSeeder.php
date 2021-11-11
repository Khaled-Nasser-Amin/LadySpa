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
            'location' => 'riyadh',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'phone' => "01025070424",
            'whatsapp' => "01025070424",
        ]);
    }
}
