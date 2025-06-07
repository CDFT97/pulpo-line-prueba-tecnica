<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /// Roles
        $free = Role::create(['name' => 'free', 'description' => 'Free user']);
        $premium = Role::create(['name' => 'premium', 'description' => 'Premium user']);

        // Permisos
        $accessWeather = Permission::create(['name' => 'access-weather-api', 'description' => 'Access weather endpoints']);

        // Asignar permisos a roles
        $premium->permissions()->attach($accessWeather);
    }
}
