<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les sites
        $vdn = Site::where('name', 'VDN')->first();
        $mariste = Site::where('name', 'Mariste')->first();
        $diamniadio = Site::where('name', 'Diamniadio')->first();

        // Créer des utilisateurs pour VDN
        User::create([
            'name' => 'Manager VDN',
            'email' => 'manager.vdn@ohgarage.sn',
            'password' => Hash::make('manager123'),
            'role' => 'stock_manager',
            'site_id' => $vdn->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Consultant VDN',
            'email' => 'consultant.vdn@ohgarage.sn',
            'password' => Hash::make('consultant123'),
            'role' => 'consultant',
            'site_id' => $vdn->id,
            'email_verified_at' => now(),
        ]);

        // Créer des utilisateurs pour Mariste
        User::create([
            'name' => 'Manager Mariste',
            'email' => 'manager.mariste@ohgarage.sn',
            'password' => Hash::make('manager123'),
            'role' => 'stock_manager',
            'site_id' => $mariste->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Consultant Mariste',
            'email' => 'consultant.mariste@ohgarage.sn',
            'password' => Hash::make('consultant123'),
            'role' => 'consultant',
            'site_id' => $mariste->id,
            'email_verified_at' => now(),
        ]);

        // Créer des utilisateurs pour Diamniadio
        User::create([
            'name' => 'Manager Diamniadio',
            'email' => 'manager.diamniadio@ohgarage.sn',
            'password' => Hash::make('manager123'),
            'role' => 'stock_manager',
            'site_id' => $diamniadio->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Consultant Diamniadio',
            'email' => 'consultant.diamniadio@ohgarage.sn',
            'password' => Hash::make('consultant123'),
            'role' => 'consultant',
            'site_id' => $diamniadio->id,
            'email_verified_at' => now(),
        ]);
    }
}
