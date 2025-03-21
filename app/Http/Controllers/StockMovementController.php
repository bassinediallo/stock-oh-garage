<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Department;
use App\Models\DepartmentStock;
use App\Models\StockMovement;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Affiche la liste des mouvements de stock.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'department.site', 'user'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('site')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('site_id', $request->site);
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $stockMovements = $query->paginate(25)->withQueryString();
        $sites = Site::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('stock-movements.index', compact('stockMovements', 'sites', 'departments'));
    }

    /**
     * Affiche le formulaire de création d'un mouvement de stock.
     */
    public function create(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $departments = Department::with('site')->orderBy('name')->get();
        $selectedProduct = $request->filled('product') ? Product::find($request->product) : null;
        $selectedDepartment = $request->filled('department') ? Department::find($request->department) : null;
        $selectedType = $request->type ?? 'entry';

        return view('stock-movements.create', compact(
            'products',
            'departments',
            'selectedProduct',
            'selectedDepartment',
            'selectedType'
        ));
    }

    /**
     * Enregistre un nouveau mouvement de stock.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'department_id' => 'required|exists:departments,id',
            'type' => 'required|in:entry,exit',
            'quantity' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $department = Department::findOrFail($validated['department_id']);
        $currentStock = $product->getCurrentStockForDepartment($department->id);

        if ($validated['type'] === 'exit' && $currentStock < $validated['quantity']) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => "Stock insuffisant. Stock actuel : {$currentStock}"]);
        }

        DB::transaction(function () use ($validated, $product, $department, $currentStock) {
            // Créer le mouvement de stock
            $stockMovement = StockMovement::create([
                'product_id' => $validated['product_id'],
                'department_id' => $validated['department_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reason' => $validated['reason'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // Mettre à jour ou créer le stock du département
            $newQuantity = $validated['type'] === 'entry' 
                ? $currentStock + $validated['quantity']
                : $currentStock - $validated['quantity'];

            DepartmentStock::updateOrCreate(
                [
                    'product_id' => $validated['product_id'],
                    'department_id' => $validated['department_id']
                ],
                ['quantity' => $newQuantity]
            );

            // Mettre à jour le statut du produit
            $product->updateTotalStock();
        });

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'Mouvement de stock enregistré avec succès');
    }

    /**
     * Affiche les détails d'un mouvement de stock.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['product', 'department.site', 'user']);
        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Exporte les mouvements de stock au format CSV.
     */
    public function export(Request $request)
    {
        $query = StockMovement::with(['product', 'department.site', 'user'])
            ->orderBy('created_at', 'desc');

        // Appliquer les filtres
        if ($request->filled('site')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('site_id', $request->site);
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $stockMovements = $query->get();

        // Générer le CSV
        $filename = 'mouvements_stock_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        // En-têtes CSV
        fputcsv($handle, [
            'Date',
            'Type',
            'Produit',
            'Référence',
            'Site',
            'Département',
            'Quantité',
            'Unité',
            'Stock après mouvement',
            'Stock minimum',
            'Motif',
            'Utilisateur'
        ]);

        // Données
        foreach ($stockMovements as $movement) {
            $stockAfterMovement = $movement->product->getStockByDepartment($movement->department_id);
            
            fputcsv($handle, [
                $movement->created_at->format('d/m/Y H:i'),
                $movement->type === 'entry' ? 'Entrée' : 'Sortie',
                $movement->product->name,
                $movement->product->reference,
                $movement->department->site->name,
                $movement->department->name,
                $movement->quantity,
                $movement->product->unit,
                $stockAfterMovement,
                $movement->product->minimum_stock,
                $movement->reason,
                $movement->user->name
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }
}
