<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'Fashion',
            'Kesehatan',
            'Olahraga',
            'Rumah Tangga'
        ];

        foreach ($categories as $category) {
            Category::create([
                'code_sku' => Str::random(3),
                'name' => $category
            ]);
        }
    }
}
