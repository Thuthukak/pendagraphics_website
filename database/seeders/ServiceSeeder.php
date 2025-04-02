<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;


class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Standard Haircut', 'description' => 'A classic haircut with styling.', 'price' => 150, 'duration' => 30],
            ['name' => 'Beard Trim', 'description' => 'Precision trimming and shaping of your beard.', 'price' => 80, 'duration' => 15],
            ['name' => 'Deluxe Haircut & Beard Trim', 'description' => 'Full haircut with a detailed beard trim.', 'price' => 200, 'duration' => 45],
            ['name' => 'Kids Haircut', 'description' => 'A neat and stylish cut for kids under 12.', 'price' => 100, 'duration' => 25],
            ['name' => 'Hot Towel Shave', 'description' => 'A relaxing hot towel shave for a smooth finish.', 'price' => 120, 'duration' => 20],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
    
}
