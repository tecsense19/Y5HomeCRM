<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrameColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            // Classic
            ['series' => 'classic', 'name' => 'Black', 'hex_code' => '#000000'],
            ['series' => 'classic', 'name' => 'White', 'hex_code' => '#F8F7F4'],
            
            // Architectural Elite
            ['series' => 'architectural-elite', 'name' => 'Black', 'hex_code' => '#000000'],
            ['series' => 'architectural-elite', 'name' => 'White', 'hex_code' => '#F8F7F4'],

            // Architectural Pro
            ['series' => 'architectural-pro', 'name' => 'Black', 'hex_code' => '#000000'],

            // Architectural Pro+
            ['series' => 'architectural-pro-plus', 'name' => 'Black', 'hex_code' => '#000000'],
            ['series' => 'architectural-pro-plus', 'name' => 'White', 'hex_code' => '#F8F7F4'],
            ['series' => 'architectural-pro-plus', 'name' => 'Grey', 'hex_code' => '#3F3F46'],
            ['series' => 'architectural-pro-plus', 'name' => 'Rose Gold', 'hex_code' => '#B76E79'],
        ];

        foreach ($colors as $c) {
            \App\Models\FrameColor::firstOrCreate([
                'series' => $c['series'],
                'name' => $c['name'],
            ], [
                'hex_code' => $c['hex_code'],
                'is_active' => true,
            ]);
        }
    }
}
