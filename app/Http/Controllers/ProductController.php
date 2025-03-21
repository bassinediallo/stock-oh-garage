<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['suppliers', 'stocks.department.site']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereHas('stocks', function($q) {
                    $q->whereRaw('quantity <= products.minimum_stock');
                });
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Tri
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        $allowedFields = ['name', 'reference', 'created_at'];
        
        if (in_array($sortField, $allowedFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query->paginate(25)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $departments = Department::with('site')->orderBy('name')->get();
        return view('products.create', compact('suppliers', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:50|unique:products',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'minimum_stock' => 'required|integer|min:0',
            'total_stock' => 'required|integer|min:0',
            'department_id' => 'required|exists:departments,id',
            'image' => 'nullable|image|max:2048',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
            'supplier_references' => 'nullable|array',
            'supplier_unit_prices' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($validated);

            // Créer un mouvement de stock initial et le stock du département
            if ($validated['total_stock'] > 0) {
                $product->stockMovements()->create([
                    'type' => 'entry',
                    'quantity' => $validated['total_stock'],
                    'reason' => 'Stock initial',
                    'department_id' => $validated['department_id'],
                    'user_id' => auth()->id()
                ]);

                $product->stocks()->create([
                    'department_id' => $validated['department_id'],
                    'quantity' => $validated['total_stock']
                ]);
            }

            // Associer les fournisseurs avec leurs références et prix unitaires
            if (!empty($validated['suppliers'])) {
                $supplierData = [];
                foreach ($validated['suppliers'] as $key => $supplierId) {
                    $supplierData[$supplierId] = [
                        'reference' => $request->input("supplier_references.{$key}"),
                        'unit_price' => $request->input("supplier_unit_prices.{$key}"),
                    ];
                }
                $product->suppliers()->attach($supplierData);
            }

            DB::commit();

            if ($product->hasLowStock()) {
                return redirect()->route('products.show', $product)
                    ->with('warning', 'Le stock initial est inférieur au stock minimum défini.')
                    ->with('success', 'Produit créé avec succès.');
            }

            return redirect()->route('products.show', $product)
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }
            \Log::error("Erreur création produit : " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création du produit.');
        }
    }

    public function show(Product $product)
    {
        $product->load(['suppliers', 'stockMovements.department.site', 'stocks.department.site']);
        $departments = Department::with('site')->orderBy('name')->get();
        
        // Calculer les statistiques de stock par département
        $departmentStats = $product->stocks->map(function ($stock) use ($product) {
            return [
                'department' => $stock->department,
                'current_stock' => $stock->quantity,
                'is_low' => $stock->quantity <= $product->minimum_stock,
                'last_movement' => $product->stockMovements()
                    ->where('department_id', $stock->department_id)
                    ->latest()
                    ->first()
            ];
        });
        
        return view('products.show', compact('product', 'departments', 'departmentStats'));
    }

    public function edit(Product $product)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $departments = Department::with('site')->orderBy('name')->get();
        return view('products.edit', compact('product', 'suppliers', 'departments'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:50|unique:products,reference,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'exists:suppliers,id',
            'supplier_references' => 'nullable|array',
            'supplier_unit_prices' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $oldImage = $product->image;

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($validated);

            // Mettre à jour les fournisseurs
            $supplierData = [];
            if (!empty($validated['suppliers'])) {
                foreach ($validated['suppliers'] as $key => $supplierId) {
                    $supplierData[$supplierId] = [
                        'reference' => $request->input("supplier_references.{$key}"),
                        'unit_price' => $request->input("supplier_unit_prices.{$key}"),
                    ];
                }
            }
            $product->suppliers()->sync($supplierData);

            // Supprimer l'ancienne image si une nouvelle a été uploadée
            if ($request->hasFile('image') && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            DB::commit();

            if ($product->hasLowStock()) {
                $lowStockDepartments = $product->getLowStockDepartments();
                $message = 'Attention : Stock bas dans les départements suivants : ';
                foreach ($lowStockDepartments as $data) {
                    $message .= "{$data['department']->name} ({$data['current_stock']} {$product->unit}), ";
                }
                return redirect()->route('products.show', $product)
                    ->with('warning', rtrim($message, ', '))
                    ->with('success', 'Produit mis à jour avec succès.');
            }

            return redirect()->route('products.show', $product)
                ->with('success', 'Produit mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du produit.');
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression du produit.');
        }
    }
}
