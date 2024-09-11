<?php

namespace Database\Seeders;

use App\Constants\RolesConstants;
use App\Models\RoleModel;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        RoleModel::firstOrCreate(
            ['code' => RolesConstants::ADMINISTRATOR],
            [
                'name' => 'Administrador',
                'description' => ''
            ]
        );

        RoleModel::firstOrCreate(
            ['code' => RolesConstants::TEST],
            [
                'name' => 'Prueba',
                'description' => ''
            ]
        );

        RoleModel::firstOrCreate(
            ['code' => RolesConstants::WORKER],
            [
                'name' => 'Trabajador',
                'description' => ''
            ]
        );
    }
}
