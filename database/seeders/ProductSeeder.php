<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all()->pluck('id')->toArray();

        $allProducts = [
            'foods' => [
                ['name' => 'fried rice', 'price' => 8000, 'description' => 'food'],
                ['name' => 'Chinese fried rice', 'price' => 8000, 'description' => 'food'],
                ['name' => 'Potato fried', 'price' => 6000, 'description' => 'food'],
                ['name' => 'Noodles', 'price' => 5000, 'description' => 'food'],
                ['name' => 'Plane Rice', 'price' => 2000, 'description' => 'food'],
                ['name' => 'Fired Water spinach', 'price' => 6000, 'description' => 'food'],
            ],
            'drinks' => [
                ['name' => 'beer', 'price' => 6000, 'description' => 'bottle'],
                ['name' => 'beer (can)', 'price' => 4000, 'description' => 'can'],
                ['name' => 'Water', 'price' => 1500, 'description' => 'bottle'],
                ['name' => 'juice', 'price' => 4000, 'description' => 'bottle'],
                ['name' => 'juice (can)', 'price' => 3000, 'description' => 'can'],
            ],
            'others' => [
                ['name' => 'Snow tower', 'price' => 5000, 'description' => 'other'],
                ['name' => 'cigarette', 'price' => 10000, 'description' => 'other'],
            ],
        ];

        foreach ($allProducts as $type => $products) {

            foreach ($branches as $id) {
                foreach ($products as $product) {
                    $product['branch_id'] = $id;
                    Product::create($product);
                }
            }

        }
    }
}