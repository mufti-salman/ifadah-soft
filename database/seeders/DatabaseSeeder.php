<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            SectionSeeder::class,
            FeeTypeSeeder::class,
            FundSeeder::class,
            LedgerSeeder::class,
            SubLedgerSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            SystemPanelSeeder::class,
            InstitutionPermissionSeeder::class,
            RoleSystemPanelSeeder::class,
            
        ]);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
