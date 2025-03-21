<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Department;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des sites
        $vdn = Site::create([
            'name' => 'VDN',
            'location' => 'VDN, Dakar',
            'phone' => '33 859 00 00',
            'email' => 'vdn@ohgarage.sn'
        ]);

        $mariste = Site::create([
            'name' => 'Mariste',
            'location' => 'Mariste, Dakar',
            'phone' => '33 859 00 01',
            'email' => 'mariste@ohgarage.sn'
        ]);

        $diamniadio = Site::create([
            'name' => 'Diamniadio',
            'location' => 'Diamniadio',
            'phone' => '33 859 00 02',
            'email' => 'diamniadio@ohgarage.sn'
        ]);

        // Création des départements
        $departments = [
            $vdn->id => [
                ['name' => 'Pièces Détachées', 'description' => 'Stock des pièces automobiles'],
                ['name' => 'Pneumatiques', 'description' => 'Stock des pneus et jantes'],
                ['name' => 'Huiles et Lubrifiants', 'description' => 'Stock des huiles moteur et autres lubrifiants']
            ],
            $mariste->id => [
                ['name' => 'Pièces Détachées', 'description' => 'Stock des pièces automobiles'],
                ['name' => 'Accessoires', 'description' => 'Stock des accessoires auto'],
                ['name' => 'Outillage', 'description' => 'Stock des outils et équipements']
            ],
            $diamniadio->id => [
                ['name' => 'Pièces Détachées', 'description' => 'Stock des pièces automobiles'],
                ['name' => 'Pneumatiques', 'description' => 'Stock des pneus et jantes'],
                ['name' => 'Outillage', 'description' => 'Stock des outils et équipements']
            ]
        ];

        foreach ($departments as $siteId => $depts) {
            foreach ($depts as $dept) {
                Department::create([
                    'site_id' => $siteId,
                    'name' => $dept['name'],
                    'description' => $dept['description']
                ]);
            }
        }

        // Création des produits
        $products = [
            ['name' => 'Filtre à huile universel', 'reference' => 'FH-001', 'description' => 'Filtre à huile compatible avec plusieurs marques', 'unit' => 'pièce', 'minimum_stock' => 10],
            ['name' => 'Plaquettes de frein avant', 'reference' => 'PF-002', 'description' => 'Plaquettes de frein haute performance', 'unit' => 'paire', 'minimum_stock' => 5],
            ['name' => 'Huile moteur 5W40', 'reference' => 'HM-003', 'description' => 'Huile moteur synthétique 5W40', 'unit' => 'litre', 'minimum_stock' => 50],
            ['name' => 'Pneu été 205/55 R16', 'reference' => 'PE-004', 'description' => 'Pneu été standard', 'unit' => 'pièce', 'minimum_stock' => 8],
            ['name' => 'Batterie 12V 60Ah', 'reference' => 'BA-005', 'description' => 'Batterie pour véhicules particuliers', 'unit' => 'pièce', 'minimum_stock' => 3],
            ['name' => 'Liquide de refroidissement', 'reference' => 'LR-006', 'description' => 'Liquide de refroidissement -35°C', 'unit' => 'litre', 'minimum_stock' => 20]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Création de mouvements de stock
        $departments = Department::all();
        $products = Product::all();

        foreach ($products as $product) {
            foreach ($departments as $department) {
                // Entrée initiale de stock
                StockMovement::create([
                    'product_id' => $product->id,
                    'department_id' => $department->id,
                    'user_id' => 1, // ID de l'admin
                    'type' => 'entry',
                    'quantity' => rand(15, 50),
                    'reason' => 'Stock initial',
                    'reference' => 'INIT-' . date('YmdHis') . rand(100, 999)
                ]);

                // Quelques sorties de stock
                for ($i = 0; $i < rand(1, 3); $i++) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'department_id' => $department->id,
                        'user_id' => 1, // ID de l'admin
                        'type' => 'exit',
                        'quantity' => rand(1, 5),
                        'reason' => 'Utilisation atelier',
                        'reference' => 'MVT-' . date('YmdHis') . rand(100, 999)
                    ]);
                }
            }
        }
    }
}
