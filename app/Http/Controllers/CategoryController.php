<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $categories = Category::query()
            ->withCount(['materials', 'questions'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('order', 'asc')
            ->orderBy('id_category', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search ?? '',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'order' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string'],
        ]);

        if (empty($validated['order'])) {
            $validated['order'] = ((int) Category::max('order')) + 1;
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($category->id_category, 'id_category'),
            ],
            'order' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string'],
        ]);

        if (empty($validated['order'])) {
            $validated['order'] = $category->order ?: (((int) Category::max('order')) + 1);
        }

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->materials()->count() > 0) {
            return redirect()->route('categories.index')->with(
                'error',
                'Kategori "'.$category->name.'" tidak dapat dihapus karena masih digunakan oleh '.$category->materials()->count().' materi pembelajaran.'
            );
        }

        if ($category->questions()->count() > 0) {
            return redirect()->route('categories.index')->with(
                'error',
                'Kategori "'.$category->name.'" tidak dapat dihapus karena masih digunakan oleh '.$category->questions()->count().' butir soal.'
            );
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
