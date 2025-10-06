<?php

namespace Database\Seeders;

use App\Models\Spot;
use Database\Factories\SpotFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Spot::factory(50)->create();
    }
}
