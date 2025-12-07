<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SpecAttributes;
use App\Models\SpecSetValue;

class specification extends Seeder
{
    public function run(): void
    {
        // Create attributes
        $brand = SpecAttributes::firstOrCreate(['name' => 'BRAND']);
        $model = SpecAttributes::firstOrCreate(['name' => 'MODEL']);
        $capacity = SpecAttributes::firstOrCreate(['name' => 'CAPACITY']);
        $resolution = SpecAttributes::firstOrCreate(['name' => 'RESOLUTION']);
        $refreshRate = SpecAttributes::firstOrCreate(['name' => 'REFRESH_RATE']);
        $type = SpecAttributes::firstOrCreate(['name' => 'TYPE']);
        $speed = SpecAttributes::firstOrCreate(['name' => 'SPEED']);

        // Create values for each attribute
        $brands = ['INTEL', 'AMD', 'NVIDIA', 'CORSAIR', 'SAMSUNG', 'ASUS', 'LOGITECH'];
        foreach ($brands as $brandName) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $brand->id, 'value' => $brandName]);
        }

        $models = ['CORE I5', 'CORE I7', 'RYZEN 5', 'RYZEN 7', 'RTX 3060', 'GTX 1660'];
        foreach ($models as $modelName) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $model->id, 'value' => $modelName]);
        }

        $capacities = ['8GB', '16GB', '32GB', '512GB', '1TB'];
        foreach ($capacities as $cap) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $capacity->id, 'value' => $cap]);
        }

        $resolutions = ['1920X1080', '2560X1440', '3840X2160'];
        foreach ($resolutions as $res) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $resolution->id, 'value' => $res]);
        }

        $refreshRates = ['60HZ', '75HZ', '144HZ'];
        foreach ($refreshRates as $rate) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $refreshRate->id, 'value' => $rate]);
        }

        $types = ['DDR4', 'DDR5', 'NVME', 'SATA', 'MECHANICAL', 'OPTICAL'];
        foreach ($types as $typeName) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $type->id, 'value' => $typeName]);
        }

        $speeds = ['2400MHZ', '3200MHZ', '3600MHZ'];
        foreach ($speeds as $speedName) {
            SpecSetValue::firstOrCreate(['spec_attributes_id' => $speed->id, 'value' => $speedName]);
        }
    }
}
