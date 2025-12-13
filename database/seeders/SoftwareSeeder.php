<?php

namespace Database\Seeders;

use App\Models\Labs;
use App\Models\Software;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftwareSeeder extends Seeder
{
    public function run(): void
    {
        $softwares = [
            ['name' => 'VISUAL STUDIO CODE', 'version' => '1.85.0', 'description' => 'Code editor'],
            ['name' => 'INTELLIJ IDEA', 'version' => '2023.3', 'description' => 'Java IDE'],
            ['name' => 'PYCHARM', 'version' => '2023.3', 'description' => 'Python IDE'],
            ['name' => 'ANDROID STUDIO', 'version' => '2023.1', 'description' => 'Android development'],
            ['name' => 'UNITY', 'version' => '2022.3', 'description' => 'Game engine'],
            ['name' => 'UNREAL ENGINE', 'version' => '5.3', 'description' => 'Game engine'],
            ['name' => 'BLENDER', 'version' => '4.0', 'description' => '3D modeling'],
            ['name' => 'ADOBE PHOTOSHOP', 'version' => '2024', 'description' => 'Image editing'],
            ['name' => 'ADOBE PREMIERE', 'version' => '2024', 'description' => 'Video editing'],
            ['name' => 'ADOBE ILLUSTRATOR', 'version' => '2024', 'description' => 'Vector graphics editor'],
            ['name' => 'ADOBE AFTER EFFECTS', 'version' => '2024', 'description' => 'Motion graphics'],
            ['name' => 'MATLAB', 'version' => 'R2023b', 'description' => 'Mathematical computing'],
            ['name' => 'MYSQL WORKBENCH', 'version' => '8.0', 'description' => 'Database management'],
            ['name' => 'POSTGRESQL', 'version' => '16.0', 'description' => 'Database system'],
            ['name' => 'MONGODB COMPASS', 'version' => '1.40', 'description' => 'MongoDB GUI'],
            ['name' => 'DOCKER', 'version' => '24.0', 'description' => 'Containerization'],
            ['name' => 'GIT', 'version' => '2.43', 'description' => 'Version control'],
            ['name' => 'POSTMAN', 'version' => '10.20', 'description' => 'API testing'],
            ['name' => 'FIGMA', 'version' => 'Desktop', 'description' => 'UI/UX design tool'],
            ['name' => 'XAMPP', 'version' => '8.2.4', 'description' => 'Local server environment'],
            ['name' => 'NODE.JS', 'version' => '20.10', 'description' => 'JavaScript runtime'],
            ['name' => 'PYTHON', 'version' => '3.12', 'description' => 'Programming language'],
            ['name' => 'JAVA JDK', 'version' => '21', 'description' => 'Java development kit'],
            ['name' => 'ECLIPSE IDE', 'version' => '2023-12', 'description' => 'Java IDE'],
            ['name' => 'NETBEANS', 'version' => '19', 'description' => 'Java IDE'],
            ['name' => 'SUBLIME TEXT', 'version' => '4', 'description' => 'Text editor'],
            ['name' => 'NOTEPAD++', 'version' => '8.6', 'description' => 'Text editor'],
            ['name' => 'FILEZILLA', 'version' => '3.66', 'description' => 'FTP client'],
            ['name' => 'WIRESHARK', 'version' => '4.2', 'description' => 'Network protocol analyzer'],
            ['name' => 'CISCO PACKET TRACER', 'version' => '8.2', 'description' => 'Network simulation'],
            ['name' => 'VMWARE WORKSTATION', 'version' => '17', 'description' => 'Virtual machine'],
            ['name' => 'VIRTUALBOX', 'version' => '7.0', 'description' => 'Virtual machine'],
            ['name' => 'ANACONDA', 'version' => '2023.09', 'description' => 'Data science platform'],
            ['name' => 'JUPYTER NOTEBOOK', 'version' => '7.0', 'description' => 'Interactive computing'],
            ['name' => 'R STUDIO', 'version' => '2023.12', 'description' => 'R IDE'],
            ['name' => 'TABLEAU', 'version' => '2023.3', 'description' => 'Data visualization'],
            ['name' => 'POWER BI', 'version' => '2.124', 'description' => 'Business analytics'],
            ['name' => 'AUTODESK MAYA', 'version' => '2024', 'description' => '3D animation software'],
            ['name' => 'AUTOCAD', 'version' => '2024', 'description' => 'CAD software'],
            ['name' => 'SOLIDWORKS', 'version' => '2023', 'description' => '3D CAD software'],
        ];

        foreach ($softwares as $software) {
            Software::firstOrCreate(['name' => $software['name']], $software);
        }

        // Attach software to labs
        $labs = Labs::all();
        $allSoftware = Software::all();
        
        foreach ($labs as $lab) {
            $softwareCount = rand(5, 10);
            $selectedSoftware = $allSoftware->random($softwareCount);
            
            foreach ($selectedSoftware as $software) {
                DB::table('lab_softwares')->insertOrIgnore([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'lab_id' => $lab->id,
                    'software_id' => $software->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
