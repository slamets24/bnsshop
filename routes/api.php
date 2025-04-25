<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories/{category}/product-count', function ($categoryId) {
        $count = Product::where('category_id', $categoryId)->count();
        return response()->json(['count' => $count]);
    });
});
