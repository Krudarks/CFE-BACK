<?php

namespace Database\Seeders;

use App\Constants\RolesConstants;
use App\Models\RoleModel;
use App\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = RoleModel::where('code', RolesConstants::ADMINISTRATOR)->first();

        UserModel::firstOrCreate(
        ['email' => config('variables.admin_email')],
        [
            'name' => 'Admin',
            'password' => Hash::make(config('variables.admin_password')),
            'role_id' => $admin->id,
        ]
    );
    }
}
