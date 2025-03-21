<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'CFAO Motors',
                'contact_person' => 'Amadou Diallo',
                'email' => 'contact@cfaomotors.sn',
                'phone' => '338234567',
                'address' => 'Route de Rufisque, Dakar',
                'notes' => 'Fournisseur principal de pièces détachées automobiles'
            ],
            [
                'name' => 'Total Energies',
                'contact_person' => 'Fatou Sow',
                'email' => 'service.client@totalenergies.sn',
                'phone' => '338765432',
                'address' => 'Boulevard du Centenaire, Dakar',
                'notes' => 'Fournisseur de lubrifiants et produits pétroliers'
            ],
            [
                'name' => 'Auto Plus',
                'contact_person' => 'Omar Sy',
                'email' => 'commandes@autoplus.sn',
                'phone' => '338901234',
                'address' => 'Avenue Bourguiba, Dakar',
                'notes' => 'Fournisseur de pièces détachées toutes marques'
            ],
            [
                'name' => 'Sénégal Pneus',
                'contact_person' => 'Marie Faye',
                'email' => 'ventes@senegalpneus.sn',
                'phone' => '338567890',
                'address' => 'Route des Niayes, Pikine',
                'notes' => 'Distributeur officiel de pneumatiques'
            ],
            [
                'name' => 'Garage Moderne',
                'contact_person' => 'Ibrahima Ndiaye',
                'email' => 'contact@garagemoderne.sn',
                'phone' => '338123456',
                'address' => 'Zone industrielle, Dakar',
                'notes' => 'Fournisseur de pièces mécaniques et électriques'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
