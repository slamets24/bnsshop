<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
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

    public function productDetail(Product $product)
    {
        return Inertia::render('Frontend/ProductDetail', [
            'product' => $product->load('category')
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
            ->paginate(12);

        return Inertia::render('Frontend/CategoryProducts', [
            'category' => $category,
            'products' => $products
        ]);
    }
}
