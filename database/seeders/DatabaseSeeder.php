<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SettingsSeeder::class,
            ProductsSeeder::class,
            ClientsSeeder::class,
            PhonesSeeder::class,
            BillsSeeder::class,
        ]);
    }
}
