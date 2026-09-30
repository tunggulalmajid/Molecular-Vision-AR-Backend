<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $materials = Material::query()
            ->with('category:id_category,name')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('id_category', $categoryId);
            })
            ->orderBy('order', 'asc')
            ->orderBy('id_material', 'asc')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()
            ->select('id_category', 'name')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Materials/Index', [
            'materials' => $materials,
            'categories' => $categories,
            'filters' => [
                'search' => $search ?? '',
                'category_id' => $categoryId ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new material.
     */
    public function create(): Response
    {
        $categories = Category::query()
            ->select('id_category', 'name')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Materials/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created material in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'id_category' => ['required', 'integer', 'exists:categories,id_category'],
            'order' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:5000'],
            'content' => ['required', 'string'],
        ]);

        if (empty($validated['order'])) {
            $maxOrder = Material::where('id_category', $validated['id_category'])->max('order') ?? 0;
            $validated['order'] = ((int) $maxOrder) + 1;
        }

        Material::create($validated);

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materi pembelajaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Material $material): Response
    {
        $material->load('category:id_category,name');

        $categories = Category::query()
            ->select('id_category', 'name')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Materials/Edit', [
            'material' => $material,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified material in storage.
     */
    public function update(Request $request, Material $material): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'id_category' => ['required', 'integer', 'exists:categories,id_category'],
            'order' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:5000'],
            'content' => ['required', 'string'],
        ]);

        if (empty($validated['order'])) {
            $maxOrder = Material::where('id_category', $validated['id_category'])->max('order') ?? 0;
            $validated['order'] = $material->order ?: (((int) $maxOrder) + 1);
        }

        $material->update($validated);

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materi pembelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified material from storage.
     */
    public function destroy(Material $material): RedirectResponse
    {
        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materi pembelajaran berhasil dihapus.');
    }

    /**
     * Handle image upload from rich text editor.
     */
    public function uploadImage(Request $request, FileStorageService $storageService): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('image');
        $uploaded = $storageService->uploadImage($file, 'materials/images');

        return response()->json([
            'url' => $uploaded['url'],
        ]);
    }
}
