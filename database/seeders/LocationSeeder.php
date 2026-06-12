<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            "Main Entrance", "Main Gate", "Foyer", "Lobby", "Drawing Room",
            "Living Room", "Family Room", "Dining Room", "Pooja Room", "Kitchen",
            "Kitchen Counter", "Dry Kitchen", "Wet Kitchen", "Pantry", "Utility",
            "Store Room", "Master Bed", "Master Bed Entrance", "Master Bed Left",
            "Master Bed Right", "Master Bath", "Walk-in Closet", "Dressing Room",
            "Dressing Entrance", "Dressing Left", "Dressing Right", "Dressing Middle",
            "Common Toilet", "Bed 1", "Bed 1 Entrance", "Bed 1 Left", "Bed 1 Right", "Bath 1",
            "Bed 2", "Bed 2 Entrance", "Bed 2 Left", "Bed 2 Right", "Bath 2",
            "Bed 3", "Bed 3 Entrance", "Bed 3 Left", "Bed 3 Right", "Bath 3",
            "Bed 4", "Bed 4 Entrance", "Bed 4 Left", "Bed 4 Right", "Bath 4",
            "Bed 5", "Bed 5 Entrance", "Bed 5 Left", "Bed 5 Right", "Bath 5",
            "Bed 6", "Bed 6 Entrance", "Bed 6 Left", "Bed 6 Right", "Bath 6",
            "Bed 7", "Bed 7 Entrance", "Bed 7 Left", "Bed 7 Right", "Bath 7",
            "Servant Room", "Servant Toilet", "Driver Room", "Guard Room",
            "Home Theater", "Gym", "Bar Area", "Lounge", "Study Room", "Play Area",
            "Office", "Conference Room", "Server Room", "Stairs", "Lift", "Verandah",
            "Balcony", "Terrace", "Terrace Garden", "Parking", "Basement Parking",
            "Car Porch", "Driveway", "Garden", "Backyard", "Swimming Pool",
            "Pool Deck", "Security Cabin"
        ];

        foreach ($tags as $tag) {
            \App\Models\Location::firstOrCreate(['name' => $tag]);
        }
    }
}
