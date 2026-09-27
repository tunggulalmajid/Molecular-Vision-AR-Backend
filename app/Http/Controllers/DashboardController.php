<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Molecule;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request to display the dashboard.
     */
    public function __invoke(Request $request): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        $stats = [
            'total_user' => $user && $user->can('users.view') ? User::count() : 0,
            'total_molekul' => $user && $user->can('molecules.view') ? Molecule::count() : 0,
            'total_materi' => $user && $user->can('materials.view') ? Material::count() : 0,
            'total_soal' => $user && $user->can('questions.view') ? Question::count() : 0,
            'total_kategori' => $user && $user->can('categories.view') ? Category::count() : 0,
            'total_role' => $user && $user->can('roles.view') ? Role::count() : 0,
        ];

        $recentMolecules = [];
        if ($user && $user->can('molecules.view')) {
            $recentMolecules = Molecule::query()
                ->select('id_molecule', 'name', 'formula', 'shape', 'created_at')
                ->latest('id_molecule')
                ->take(5)
                ->get();
        }

        $recentMaterials = [];
        if ($user && $user->can('materials.view')) {
            $recentMaterials = Material::query()
                ->with('category:id_category,name')
                ->select('id_material', 'name', 'id_category', 'created_at')
                ->latest('id_material')
                ->take(5)
                ->get();
        }

        $recentQuestions = [];
        if ($user && $user->can('questions.view')) {
            $recentQuestions = Question::query()
                ->with('category:id_category,name')
                ->select('id_question', 'name', 'id_category', 'question_type', 'created_at')
                ->latest('id_question')
                ->take(5)
                ->get();
        }

        $recentCategories = [];
        if ($user && $user->can('categories.view')) {
            $recentCategories = Category::query()
                ->select('id_category', 'name')
                ->withCount(['materials', 'questions'])
                ->latest('id_category')
                ->take(5)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentMolecules' => $recentMolecules,
            'recentMaterials' => $recentMaterials,
            'recentQuestions' => $recentQuestions,
            'recentCategories' => $recentCategories,
        ]);
    }
}
