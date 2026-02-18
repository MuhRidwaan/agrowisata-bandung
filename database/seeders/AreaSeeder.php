<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run()
    {
        Area::create(['name' => 'Pengalengan']);
        Area::create(['name' => 'Lembang']);
        Area::create(['name' => 'Ciwidey']);
        Area::create(['name' => 'Cisarua']);
    }
}
