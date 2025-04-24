<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone X Pro',
                'description' => 'Smartphone terbaru dengan fitur canggih dan kamera berkualitas tinggi.',
                'price' => 8999000,
                'stock' => 50,
                'category' => 'Elektronik',
                'sku' => 'SPXPRO001'
            ],
            [
                'name' => 'Sepatu Lari Premium',
                'description' => 'Sepatu lari dengan teknologi terbaru untuk kenyamanan maksimal.',
                'price' => 1299000,
                'stock' => 30,
                'category' => 'Olahraga',
                'sku' => 'SLPREM001'
            ],
            [
                'name' => 'Blender Multifungsi',
                'description' => 'Blender dengan berbagai fungsi untuk kebutuhan dapur Anda.',
                'price' => 499000,
                'stock' => 25,
                'category' => 'Rumah Tangga',
                'sku' => 'BLMULT001'
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'description' => 'Suplemen vitamin C untuk meningkatkan daya tahan tubuh.',
                'price' => 150000,
                'stock' => 100,
                'category' => 'Kesehatan',
                'sku' => 'VITC001'
            ],
            [
                'name' => 'Kemeja Casual',
                'description' => 'Kemeja casual dengan bahan nyaman dan desain modern.',
                'price' => 299000,
                'stock' => 40,
                'category' => 'Fashion',
                'sku' => 'KMCAS001'
            ]
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'sku' => $productData['sku'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'category_id' => $category->id
            ]);

            // Create product images
            for ($i = 1; $i <= 5; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => "https://picsum.photos/800/800?random=" . $product->id . $i
                ]);
            }

            // Create product links
            ProductLink::create([
                'product_id' => $product->id,
                'shopee_url' => 'https://shopee.co.id/product-' . $product->id,
                'tokopedia_url' => 'https://tokopedia.com/product-' . $product->id,
                'whatsapp_number' => '6281234567890'
            ]);
        }
    }
}
