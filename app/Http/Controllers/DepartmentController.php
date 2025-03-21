<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Site;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $departments = Department::with(['site', 'stocks.product'])->get();

         // Calculer le nombre de produits et la valeur totale
    foreach ($departments as $department) {
        $department->product_count = $department->stocks->count(); // Nombre de produits
        $department->total_value = $department->stocks->reduce(function ($carry, $stock) {
            return $carry + ($stock->product->price * $stock->quantity); // Calcul de la valeur totale
        }, 0);
    }
    
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create', Department::class);
        $sites = Site::all();
        return view('departments.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Department::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'site_id' => 'required|exists:sites,id',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Département créé avec succès.');
    }

    public function show(Department $department)
    {
        $stocks = $department->stocks()->with('product')->get();
        $recentMovements = $department->stockMovements()
            ->with(['product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('departments.show', compact('department', 'stocks', 'recentMovements'));
    }

    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        $sites = Site::all();
        return view('departments.edit', compact('department', 'sites'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'site_id' => 'required|exists:sites,id',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Département mis à jour avec succès.');
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Département supprimé avec succès.');
    }
}
