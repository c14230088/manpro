<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Monitor', 'CPU', 'Keyboard', 'Mouse', 'VR Headset',
            'Processor', 'RAM', 'Storage', 'Graphics Card', 'Motherboard',
            'Power Supply', 'Case', 'Cooling Fan', 'Network Card',
            'Sound Card', 'Webcam', 'Speaker', 'Headphone', 'Microphone'
        ];

        foreach ($types as $type) {
            Type::firstOrCreate(['name' => strtoupper($type)]);
        }
    }
}