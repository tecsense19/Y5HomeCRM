<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = [
            ['name' => 'Light', 'image_path' => 'system:light'],
            ['name' => 'Fan', 'image_path' => 'system:fan'],
            ['name' => 'AC', 'image_path' => 'system:ac'],
            ['name' => 'Bell', 'image_path' => 'system:bell'],
            ['name' => 'Dimmer', 'image_path' => 'system:dimmer'],
            ['name' => '5-pin socket', 'image_path' => 'system:socket'],
            ['name' => 'USB-C', 'image_path' => 'system:usbc'],
            ['name' => 'Camera', 'image_path' => 'system:camera'],
            ['name' => 'Wi-Fi', 'image_path' => 'system:wifi'],
            ['name' => 'TV', 'image_path' => 'system:tv'],
            ['name' => 'Speaker', 'image_path' => 'system:speaker'],
            ['name' => 'Power', 'image_path' => 'system:power'],
            ['name' => 'Door', 'image_path' => 'system:door'],
            ['name' => 'Thermostat', 'image_path' => 'system:temp'],
            ['name' => 'Home', 'image_path' => 'system:home'],
            ['name' => 'Out', 'image_path' => 'system:out'],
            ['name' => 'Sleep', 'image_path' => 'system:sleep'],
            ['name' => 'DIY', 'image_path' => 'system:diy'],
        ];

        foreach ($icons as $icon) {
            \App\Models\Icon::firstOrCreate(
                ['name' => $icon['name']],
                ['image_path' => $icon['image_path'], 'is_active' => true]
            );
        }
    }
}
