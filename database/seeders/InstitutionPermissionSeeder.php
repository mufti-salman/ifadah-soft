<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Madrasa;
use App\Models\Permission;

class InstitutionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Get Active Permissions
        |--------------------------------------------------------------------------
        |
        | সব active system permission institution-এর জন্য enable করা হবে।
        |
        */

        $permissionIds = Permission::where('is_active', true)
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Get Active Institutions
        |--------------------------------------------------------------------------
        |
        | বর্তমানে যেসব active institution আছে,
        | তাদের জন্য initial permissions enable করা হবে।
        |
        */

        $institutions = Madrasa::active()->get();


        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($institutions as $institution) {

            /*
            |--------------------------------------------------------------------------
            | syncWithoutDetaching()
            |--------------------------------------------------------------------------
            |
            | আগে থেকে থাকা permission মুছে যাবে না।
            | নতুন permission থাকলে শুধু সেটি যোগ হবে।
            |
            */

            $institution->permissions()->syncWithoutDetaching(
                $permissionIds
            );
        }
    }
}