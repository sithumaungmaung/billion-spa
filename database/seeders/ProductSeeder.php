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
                ['name' => 'fried rice', 'product_code' => 'FR-001' ,'price' => 8000, 'description' => 'food'],
                ['name' => 'Chinese fried rice', 'product_code' => 'FR-002', 'price' => 8000, 'description' => 'food'],
                ['name' => 'Potato fried', 'product_code' => 'FR-003', 'price' => 6000, 'description' => 'food'],
                ['name' => 'Noodles', 'product_code' => 'FR-004', 'price' => 5000, 'description' => 'food'],
                ['name' => 'Plane Rice', 'product_code' => 'FR-005', 'price' => 2000, 'description' => 'food'],
                ['name' => 'Fired Water spinach', 'product_code' => 'FR-006', 'price' => 6000, 'description' => 'food'],
            ],
            'drinks' => [
                ['name' => 'beer', 'product_code' => 'B-001', 'price' => 6000, 'description' => 'bottle'],
                ['name' => 'beer (can)',  'product_code' => 'B-002', 'price' => 4000, 'description' => 'can'],
                ['name' => 'Water',  'product_code' => 'B-003', 'price' => 1500, 'description' => 'bottle'],
                ['name' => 'juice',  'product_code' => 'B-004', 'price' => 4000, 'description' => 'bottle'],
                ['name' => 'juice (can)',  'product_code' => 'B-005', 'price' => 3000, 'description' => 'can'],
            ],
            'others' => [
                ['name' => 'Snow tower',  'product_code' => 'O-001', 'price' => 5000, 'description' => 'other'],
                ['name' => 'cigarette',  'product_code' => 'O-002', 'price' => 10000, 'description' => 'other'],
                ['name' => 'Tissue',  'product_code' => 'O-003', 'price' => 1000, 'description' => 'other'],
            ],
        ];

        foreach ($branches as $id) {
            foreach ($allProducts as $type => $products) {

                foreach ($products as $product) {
                    Product::firstOrCreate([
                        'branch_id' => $id,
                        'name' => $product['name'], // unique per branch
                        'product_code' => $product['product_code'],
                    ],
                    [
                        'price' => $product['price'],
                        'description' => $product['description'],
                    ]);
                }
            }

        }
    }
}
