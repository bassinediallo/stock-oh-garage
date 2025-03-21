<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Department;
use App\Models\Site;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $products = $query->paginate(25)->withQueryString();

        $departments = Department::with('site')->orderBy('name')->get();
        $sites = Site::orderBy('name')->get();
        $selectedDepartment = $request->input('department_id');
        $selectedSite = $request->input('site_id');

        return view('reports.stock', compact(
            'products',
            'departments',
            'sites',
            'selectedDepartment',
            'selectedSite'
        ));
    }

    public function export(Request $request)
    {
        $query = $this->buildQuery($request);
        $products = $query->get();

        // Créer un nouveau document Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Style pour les en-têtes
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0066CC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // Style pour les cellules de données
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Style pour les stocks faibles
        $lowStockStyle = [
            'font' => [
                'color' => ['rgb' => 'FF0000'],
                'bold' => true,
            ],
        ];

        // En-têtes
        $headers = [
            'A1' => 'Référence',
            'B1' => 'Produit',
            'C1' => 'Site',
            'D1' => 'Département',
            'E1' => 'Stock actuel',
            'F1' => 'Stock minimum',
            'G1' => 'Unité',
            'H1' => 'Statut',
            'I1' => 'Dernier mouvement',
            'J1' => 'Type',
            'K1' => 'Date'
        ];

        // Appliquer les en-têtes et leur style
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Données
        $row = 2;
        foreach ($products as $product) {
            foreach ($product->stocks as $stock) {
                $lastMovement = $product->stockMovements()
                    ->where('department_id', $stock->department_id)
                    ->latest()
                    ->first();

                $isLowStock = $stock->quantity <= $product->minimum_stock;
                $status = $isLowStock ? 'Stock bas' : 'Stock OK';

                // Remplir les données
                $sheet->setCellValue('A' . $row, $product->reference);
                $sheet->setCellValue('B' . $row, $product->name);
                $sheet->setCellValue('C' . $row, $stock->department->site->name);
                $sheet->setCellValue('D' . $row, $stock->department->name);
                $sheet->setCellValue('E' . $row, $stock->quantity);
                $sheet->setCellValue('F' . $row, $product->minimum_stock);
                $sheet->setCellValue('G' . $row, $product->unit);
                $sheet->setCellValue('H' . $row, $status);
                $sheet->setCellValue('I' . $row, $lastMovement ? $lastMovement->quantity : '-');
                $sheet->setCellValue('J' . $row, $lastMovement ? ($lastMovement->type === 'entry' ? 'Entrée' : 'Sortie') : '-');
                $sheet->setCellValue('K' . $row, $lastMovement ? $lastMovement->created_at->format('d/m/Y H:i') : '-');

                // Appliquer le style de base
                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray($dataStyle);

                // Appliquer le style de stock faible si nécessaire
                if ($isLowStock) {
                    $sheet->getStyle('E' . $row)->applyFromArray($lowStockStyle);
                }

                $row++;
            }
        }

        // Ajuster la largeur des colonnes automatiquement
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Créer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'rapport_stock_' . now()->format('Y-m-d_His') . '.xlsx';
        
        // Headers pour le téléchargement
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Envoyer le fichier
        $writer->save('php://output');
        exit;
    }

    private function buildQuery(Request $request)
    {
        $query = Product::with(['stocks.department.site', 'stockMovements']);

        // Filtre par site
        if ($request->filled('site_id')) {
            $query->whereHas('stocks.department', function ($q) use ($request) {
                $q->where('site_id', $request->site_id);
            });
        }

        // Filtre par département
        if ($request->filled('department_id')) {
            $query->whereHas('stocks', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filtre par niveau de stock
        if ($request->input('stock_level') === 'low') {
            $query->whereHas('stocks', function ($q) {
                $q->whereRaw('quantity <= products.minimum_stock');
            });
        }

        // Recherche par nom ou référence
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        $allowedFields = ['name', 'reference', 'created_at'];
        
        if (in_array($sortField, $allowedFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }
}
