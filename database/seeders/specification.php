<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecAttributes;
use App\Models\SpecSet;
use App\Models\SpecSetValue;
use App\Models\SpecType;


class specification extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $typeProcessor = SpecType::firstOrCreate(['name' => 'Processor']);
        $typeRAM = SpecType::firstOrCreate(['name' => 'RAM']);
        $typeStorage = SpecType::firstOrCreate(['name' => 'Storage']);
        $typeCPU = SpecType::firstOrCreate(['name' => 'CPU']);
        $typeMonitor = SpecType::firstOrCreate(['name' => 'Monitor']);
        $typeMouse = SpecType::firstOrCreate(['name' => 'Mouse']);
        $typeKeyboard = SpecType::firstOrCreate(['name' => 'Keyboard']);

        // Atribut untuk Processor
        $attrCpuBrand = SpecAttributes::firstOrCreate(['spec_type_id' => $typeProcessor->id, 'name' => 'Brand']);
        $attrCpuModel = SpecAttributes::firstOrCreate(['spec_type_id' => $typeProcessor->id, 'name' => 'Model']);
        $attrCpuGen = SpecAttributes::firstOrCreate(['spec_type_id' => $typeProcessor->id, 'name' => 'Generation']);

        // Atribut untuk RAM
        $attrRamType = SpecAttributes::firstOrCreate(['spec_type_id' => $typeRAM->id, 'name' => 'Type']);
        $attrRamCapacity = SpecAttributes::firstOrCreate(['spec_type_id' => $typeRAM->id, 'name' => 'Capacity']);
        $attrRamBrand = SpecAttributes::firstOrCreate(['spec_type_id' => $typeRAM->id, 'name' => 'Brand']);

        // Atribut untuk Storage
        $attrStorageType = SpecAttributes::firstOrCreate(['spec_type_id' => $typeStorage->id, 'name' => 'Type']);
        $attrStorageCapacity = SpecAttributes::firstOrCreate(['spec_type_id' => $typeStorage->id, 'name' => 'Capacity']);
        $attrStorageInterface = SpecAttributes::firstOrCreate(['spec_type_id' => $typeStorage->id, 'name' => 'Interface']);
        $attrStorageBrand = SpecAttributes::firstOrCreate(['spec_type_id' => $typeStorage->id, 'name' => 'Brand']);

        //atribut untuk monitor
        $attrMonitorRes = SpecAttributes::firstOrCreate(['spec_type_id' => $typeMonitor->id, 'name' => 'Resolution']);
        $attrMonitorRefresh = SpecAttributes::firstOrCreate(['spec_type_id' => $typeMonitor->id, 'name' => 'Refresh_rate']);

        //atribut untuk mouse
        $attrMouseDpi = SpecAttributes::firstOrCreate(['spec_type_id' => $typeMouse->id, 'name' => 'Dpi']);
        $attrMouseColor = SpecAttributes::firstOrCreate(['spec_type_id' => $typeMouse->id, 'name' => 'Color']);


        //atribut untuk keyboard
        $attrKeyboardLayout = SpecAttributes::firstOrCreate(['spec_type_id' => $typeKeyboard->id, 'name' => 'Layout']);
        $attrKeyboardSwitch = SpecAttributes::firstOrCreate(['spec_type_id' => $typeKeyboard->id, 'name' => 'Switch']);


        //atribut untuk CPU


        $presetCPU = SpecSet::firstOrCreate(
            ['display_name' => 'Intel Core i5 10th Gen'],
            ['spec_type_id' => $typeProcessor->id]
        );

        $presetRAM = SpecSet::firstOrCreate(
            ['display_name' => 'Corsair 8GB DDR4'],
            ['spec_type_id' => $typeRAM->id]
        );

        $presetSSD = SpecSet::firstOrCreate(
            ['display_name' => 'Samsung 512GB NVMe PCIe 3.0'],
            ['spec_type_id' => $typeStorage->id]
        );

        $presetMonitor = SpecSet::firstOrCreate(
            ['display_name' => '1920x1080 75Hz'],
            ['spec_type_id' => $typeMonitor->id]
        );
        $presetMouse = SpecSet::firstOrCreate(
            ['display_name' => 'Dpi 800-8000 Black'],
            ['spec_type_id' => $typeMouse->id]
        );
        $presetKeyboard = SpecSet::firstOrCreate(
            ['display_name' => 'TKL Blue'],
            ['spec_type_id' => $typeKeyboard->id]
        );


        SpecSetValue::firstOrCreate(['spec_set_id' => $presetCPU->id, 'spec_attributes_id' => $attrCpuBrand->id, 'value' => 'Intel']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetCPU->id, 'spec_attributes_id' => $attrCpuModel->id, 'value' => 'Core i5']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetCPU->id, 'spec_attributes_id' => $attrCpuGen->id, 'value' => '10th Gen']);

        SpecSetValue::firstOrCreate(['spec_set_id' => $presetRAM->id, 'spec_attributes_id' => $attrRamType->id, 'value' => 'DDR4']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetRAM->id, 'spec_attributes_id' => $attrRamCapacity->id, 'value' => '8GB']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetRAM->id, 'spec_attributes_id' => $attrRamBrand->id, 'value' => 'Corsair']);

        SpecSetValue::firstOrCreate(['spec_set_id' => $presetSSD->id, 'spec_attributes_id' => $attrStorageType->id, 'value' => 'NVMe SSD']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetSSD->id, 'spec_attributes_id' => $attrStorageCapacity->id, 'value' => '512GB']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetSSD->id, 'spec_attributes_id' => $attrStorageInterface->id, 'value' => 'PCIe 3.0']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetSSD->id, 'spec_attributes_id' => $attrStorageBrand->id, 'value' => 'Samsung']);

        SpecSetValue::firstOrCreate(['spec_set_id' => $presetMonitor->id, 'spec_attributes_id' => $attrMonitorRes->id, 'value' => '1920x1080']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetMonitor->id, 'spec_attributes_id' => $attrMonitorRefresh->id, 'value' => '75Hz']);
        
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetMouse->id, 'spec_attributes_id' => $attrMouseDpi->id, 'value' => '800-8000']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetMouse->id, 'spec_attributes_id' => $attrMouseColor->id, 'value' => 'Black']);
        
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetKeyboard->id, 'spec_attributes_id' => $attrKeyboardLayout->id, 'value' => 'TKL']);
        SpecSetValue::firstOrCreate(['spec_set_id' => $presetKeyboard->id, 'spec_attributes_id' => $attrKeyboardSwitch->id, 'value' => 'Blue']);

    }
}
