<?php

namespace Database\Seeders;

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
        $this->call([

            // System structure
            SystemPanelSeeder::class,

            // All permissions
            PermissionSeeder::class,

            // Roles + Role Permissions + Role Panels
            // + Institutions + Users
            RoleSeeder::class,

            // Institution / application data
            InstitutionPermissionSeeder::class,

            // Academic & accounting data
            SectionSeeder::class,
            FeeTypeSeeder::class,
            FundSeeder::class,
            LedgerSeeder::class,
            SubLedgerSeeder::class,
        ]);
    }
}