<?php

namespace Database\Seeders;

use App\Models\Scheduler\SroEquipmentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SroEquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = File::json(base_path() . '/database/seeders/seeds/sro-equipment-categories.json');

        foreach($data as $category)
        {
            SroEquipmentCategory::updateOrCreate([
                'sro_category_id' => $category['id']
            ],[
                'name' => $category['name']
            ]);
        }
    }
}
