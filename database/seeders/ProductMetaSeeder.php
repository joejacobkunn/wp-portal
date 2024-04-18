<?php

namespace Database\Seeders;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //seed brands first
        echo "Seeding Brands";
        $this->seedBrands();
        //seed category 
        echo "Seeding Category";
        $this->seedCategory();
        //seed vendors
        echo "Seeding Vendors";
        $this->seedVendors();

    }

    private function seedBrands()
    {
        $brands = DB::connection('sx')->select("SELECT DISTINCT user3 AS brand FROM pub.icsl with(nolock)");

        foreach($brands as $brand)
        {
            Brand::firstOrCreate([
                'name' => $brand->BRAND ?: 'Unknown'
            ]);
        }
    }

    private function seedCategory()
    {
        $categories = DB::connection('sx')->select("SELECT DISTINCT prodcat AS ProdCat FROM pub.icsp with(nolock)");

        foreach($categories as $category)
        {
            Category::firstOrCreate([
                'name' => $category->PRODCAT ?? 'Unknown'
            ]);
        }
    }

    private function seedVendors()
    {
        $vendors = DB::connection('sx')->select("SELECT name,vendno FROM pub.apsv WITH(nolock)");

        foreach($vendors as $vendor)
        {
            Vendor::firstOrCreate([
                'name' => $vendor->name ?: 'Unknown',  'vendor_number' => $vendor->vendno ?: 0
            ]);
        }
    }


}
