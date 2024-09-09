<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Address\Country;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Country::create([
            "name" => "Việt Nam",
            "code" => "VN",
            "phone_code" => "+84",
            "currency" => "VND",
            "language" => "vi",
        ]);
    }
}
