<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;

class FrontendController extends Controller
{
    public function welcome()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->paginate(12);

        $favoritProducts = Product::where('is_active', true)
            ->with('category')
            ->take(4)
            ->get();

        return Inertia::render('Frontend/Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'products' => $products,
            'favoritProducts' => $favoritProducts
        ]);
    }

    public function products()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->paginate(12);

        return Inertia::render('Frontend/Products', [
            'products' => $products
        ]);
    }

    public function categories()
    {
        $categories = Category::withCount('products')->get();

        return Inertia::render('Frontend/Categories', [
            'categories' => $categories
        ]);
    }

    public function categoryProducts(Category $category)
    {
        $products = $category->products()
            ->where('is_active', true)
            ->with('category')
            ->paginate(12);

        return Inertia::render('Frontend/CategoryProducts', [
            'category' => $category,
            'products' => $products
        ]);
    }

    public function productDetail(Category $category, Product $product)
    {
        // Debug
        info('Category:', ['category' => $category->toArray()]);
        info('Product:', ['product' => $product->toArray()]);

        if ($product->category_id !== $category->id) {
            abort(404);
        }

        // Get related products from same category, exclude current product
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->take(4)
            ->get();

        // Load category relationship
        $product->load(['category', 'images']);

        return Inertia::render('Frontend/ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}
