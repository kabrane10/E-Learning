<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index');
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée avec succès');
    }

    public function edit($id)
    {
        return view('admin.categories.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée');
    }

    public function toggleStatus($id)
    {
        return back()->with('success', 'Statut modifié');
    }

    public function bulkAction(Request $request)
    {
        return back()->with('success', 'Action groupée effectuée');
    }
}