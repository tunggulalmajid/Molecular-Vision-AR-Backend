<?php

namespace App\Http\Controllers;

use App\Models\Molecule;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class MoleculeController extends Controller
{
    /**
     * Display a listing of molecules.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $shape = $request->input('shape');

        $molecules = Molecule::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('formula', 'like', "%{$search}%")
                        ->orWhere('shape', 'like', "%{$search}%")
                        ->orWhere('bond_type', 'like', "%{$search}%");
                });
            })
            ->when($shape, function ($query, $shape) {
                $query->where('shape', $shape);
            })
            ->latest('id_molecule')
            ->paginate(10)
            ->withQueryString();

        // Get unique list of shapes for filtering
        $availableShapes = Molecule::query()
            ->select('shape')
            ->distinct()
            ->whereNotNull('shape')
            ->orderBy('shape')
            ->pluck('shape');

        return Inertia::render('Molecules/Index', [
            'molecules' => $molecules,
            'availableShapes' => $availableShapes,
            'filters' => [
                'search' => $search ?? '',
                'shape' => $shape ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new molecule.
     */
    public function create(): Response
    {
        return Inertia::render('Molecules/Create');
    }

    /**
     * Store a newly created molecule in storage.
     */
    public function store(Request $request, FileStorageService $storageService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'formula' => ['required', 'string', 'max:100'],
            'shape' => ['required', 'string', 'max:150'],
            'bent' => ['required', 'string', 'max:100'],
            'bond_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'model_3d_file' => ['required', 'file', 'max:51200'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('model_3d_file');
        $uploaded = $storageService->upload3DModel($file, 'molecules/models');

        Molecule::create([
            'name' => $validated['name'],
            'formula' => $validated['formula'],
            'shape' => $validated['shape'],
            'bent' => $validated['bent'],
            'bond_type' => $validated['bond_type'],
            'description' => $validated['description'],
            'model_3d_url' => $uploaded['url'],
        ]);

        return redirect()
            ->route('molecules.index')
            ->with('success', 'Model molekul kimia berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified molecule.
     */
    public function edit(Molecule $molecule): Response
    {
        return Inertia::render('Molecules/Edit', [
            'molecule' => $molecule,
        ]);
    }

    /**
     * Update the specified molecule in storage.
     */
    public function update(Request $request, Molecule $molecule, FileStorageService $storageService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'formula' => ['required', 'string', 'max:100'],
            'shape' => ['required', 'string', 'max:150'],
            'bent' => ['required', 'string', 'max:100'],
            'bond_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'model_3d_file' => ['nullable', 'file', 'max:51200'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'formula' => $validated['formula'],
            'shape' => $validated['shape'],
            'bent' => $validated['bent'],
            'bond_type' => $validated['bond_type'],
            'description' => $validated['description'],
        ];

        if ($request->hasFile('model_3d_file')) {
            /** @var UploadedFile $file */
            $file = $request->file('model_3d_file');
            $uploaded = $storageService->upload3DModel($file, 'molecules/models');

            // Delete old file from S3 if exists
            if ($molecule->model_3d_url) {
                $storageService->deleteFile($molecule->model_3d_url);
            }

            $updateData['model_3d_url'] = $uploaded['url'];
        }

        $molecule->update($updateData);

        return redirect()
            ->route('molecules.index')
            ->with('success', 'Model molekul kimia berhasil diperbarui.');
    }

    /**
     * Remove the specified molecule from storage.
     */
    public function destroy(Molecule $molecule): RedirectResponse
    {
        $molecule->delete();

        return redirect()
            ->route('molecules.index')
            ->with('success', 'Molekul berhasil dihapus.');
    }
}
