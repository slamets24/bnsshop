<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code_sku', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->input('perPage', 5);

        return Inertia::render('Dashboard/categories/Index', [
            'categories' => $query->latest()->paginate($perPage),
            'filters' => $request->only(['search', 'perPage']),
            'message' => session('message')
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/categories/Create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'code_sku' => 'required|string|max:255|unique:categories,code_sku'
            ]);

            DB::beginTransaction();

            Category::create($validated);

            DB::commit();

            return redirect()->route('dashboard.categories.index')
                ->with('message', 'Kategori berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal membuat kategori: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        return Inertia::render('Dashboard/categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'code_sku' => 'required|string|max:255|unique:categories,code_sku,' . $category->id
            ]);

            DB::beginTransaction();

            $category->update($validated);

            DB::commit();

            return redirect()->route('dashboard.categories.index')
                ->with('message', 'Kategori berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('dashboard.categories.index')
            ->with('message', 'Kategori berhasil dihapus');
    }
}
