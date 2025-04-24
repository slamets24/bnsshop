<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('perPage', 10);

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
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        Category::create($validated);

        return redirect()->route('dashboard.categories.index')
            ->with('message', 'Kategori berhasil dibuat');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Dashboard/categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);

        $category->update($validated);

        return redirect()->route('dashboard.categories.index')
            ->with('message', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('dashboard.categories.index')
            ->with('message', 'Kategori berhasil dihapus');
    }
}
