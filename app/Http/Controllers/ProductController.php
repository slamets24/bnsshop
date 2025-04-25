<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images','links']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('Dashboard/products/Index', [
            'products' => $query->latest()->paginate($perPage),
            'filters' => $request->only(['search', 'perPage']),
            'message' => session('message')
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/products/Create', [
            'categories' => Category::all()
        ]);
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create product with basic information
            $product = $this->createProduct($request->validated());

            // Store product links (marketplace links)
            $this->storeProductLinks($product, $request->validated());

            // Store product images
            $this->storeProductImages($product, $request);

            DB::commit();

            return redirect()->route('dashboard.products.index')
                ->with('message', 'Produk berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal membuat produk: ' . $e->getMessage())->withErrors(['error' => 'Gagal Menambahkan Produk: ' . $e->getMessage()]);
        }
    }

    protected function generateSKU($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $productCount = Product::where('category_id', $categoryId)->count() + 1;
        return $category->code_sku . '-' . str_pad($productCount, 3, '0', STR_PAD_LEFT);
    }

    protected function createProduct($validated)
    {
        return Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sku' => $this->generateSKU($validated['category_id']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
        ]);
    }

    protected function storeProductLinks($product, $validated)
    {
        $product->links()->create([
            'shopee_url' => $validated['shopee_url'] ?? null,
            'tokopedia_url' => $validated['tokopedia_url'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
        ]);
    }

    protected function storeProductImages($product, $request)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_url' => $path
                ]);
            }
        }
    }

    public function edit(Product $product)
    {
        return Inertia::render('Dashboard/products/Edit', [
            'product' => $product->load(['category', 'images', 'links']),
            'categories' => Category::all()
        ]);
    }

    protected function updateProduct(Product $product, $validated)
    {
        $updateData = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
        ];

        // Only generate new SKU if category changed
        if ($product->category_id != $validated['category_id']) {
            $updateData['sku'] = $this->generateSKU($validated['category_id']);
        }

        $product->update($updateData);
    }

    protected function updateProductLinks(Product $product, $validated)
    {
        $product->links()->update([
            'shopee_url' => $validated['shopee_url'] ?? null,
            'tokopedia_url' => $validated['tokopedia_url'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'shopee_url' => 'nullable|string|url',
                'tokopedia_url' => 'nullable|string|url',
                'whatsapp_number' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            DB::beginTransaction();

            // Update product basic information
            $this->updateProduct($product, $validated);

            // Update product links
            $this->updateProductLinks($product, $validated);

            // Store new product images
            $this->storeProductImages($product, $request);

            DB::commit();

            return redirect()->route('dashboard.products.index')
                ->with('message', 'Produk berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('dashboard.products.index')
            ->with('message', 'Produk berhasil dihapus');
    }
}
