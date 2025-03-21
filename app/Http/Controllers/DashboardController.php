<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\Site;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer les départements avec leurs statistiques
        $departments = Department::with('site', 'stocks')
            ->withCount(['stocks as products_count' => function ($query) {
                $query->where('quantity', '>', 0);
            }])
            ->withCount(['stocks as low_stock_count' => function ($query) {
                $query->join('products', 'department_stocks.product_id', '=', 'products.id')
                    ->where('quantity', '>', 0)
                    ->whereColumn('quantity', '<=', 'products.minimum_stock');
            }])
            ->get();

        // Récupérer les produits en stock faible avec leurs départements
        $lowStockProducts = Product::where('status', 'Stock faible')
            ->with(['stocks.department'])
            ->limit(5)
            ->get()
            ->map(function ($product) {
                $totalStock = $product->getTotalStock();
                $departments = $product->stocks()
                    ->where('quantity', '>', 0)
                    ->with('department')
                    ->get()
                    ->map(function ($stock) {
                        return [
                            'name' => $stock->department->name,
                            'quantity' => $stock->quantity
                        ];
                    });

                return [
                    'product' => $product,
                    'total_stock' => $totalStock,
                    'departments' => $departments
                ];
            });

        // Récupérer les mouvements récents
        $recentMovements = StockMovement::with(['product', 'department', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        // Données pour le graphique des mouvements
        $movementsChartData = $this->getMovementsChartData();

        // Données pour le graphique des stocks par site
        $stocksChartData = $this->getStocksChartData();

        return view('dashboard', compact(
            'departments',
            'lowStockProducts',
            'recentMovements',
            'movementsChartData',
            'stocksChartData'
        ));
    }

    private function getMovementsChartData()
    {
        $movements = StockMovement::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(CASE WHEN type = "entry" THEN 1 END) as entries'),
            DB::raw('COUNT(CASE WHEN type = "exit" THEN 1 END) as exits')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();

        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $data = [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('d/m')),
            'entries' => $dates->map(function ($date) use ($movements) {
                $movement = $movements->firstWhere('date', $date);
                return $movement ? $movement->entries : 0;
            }),
            'exits' => $dates->map(function ($date) use ($movements) {
                $movement = $movements->firstWhere('date', $date);
                return $movement ? $movement->exits : 0;
            })
        ];

        return $data;
    }

    private function getStocksChartData()
    {
        $sites = Site::with(['departments.stocks' => function ($query) {
            $query->where('quantity', '>', 0);
        }])->get();

        return [
            'labels' => $sites->pluck('name'),
            'data' => $sites->map(function ($site) {
                return $site->departments->sum(function ($department) {
                    return $department->stocks->sum('quantity');
                });
            })
        ];
    }
}
