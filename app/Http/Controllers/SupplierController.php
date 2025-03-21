<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Affiche la liste des fournisseurs.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Affiche le formulaire de création d'un fournisseur.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Enregistre un nouveau fournisseur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $supplier = Supplier::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier
            ]);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un fournisseur.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('products');
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Affiche le formulaire de modification d'un fournisseur.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Met à jour un fournisseur.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Supprime un fournisseur.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }

    /**
     * Retourne la liste des fournisseurs pour un select2.
     */
    public function search(Request $request)
    {
        $term = $request->get('term');
        $suppliers = Supplier::where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name as text']);

        return response()->json(['results' => $suppliers]);
    }

    /**
     * Ajoute un fournisseur via une requête AJAX.
     */
    public function quickStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier = Supplier::create($validator->validated());

        return response()->json([
            'success' => true,
            'supplier' => [
                'id' => $supplier->id,
                'text' => $supplier->name
            ]
        ]);
    }
}
