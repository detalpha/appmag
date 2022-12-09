<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::firstOrCreate([
        	'id' => 1,
            'name' => 'Administrator', 
            'slug' => 'superadmin',
            'permissions' => [
                'manage-pegawai' => true,
            ]
        ]);

        $user = Role::firstOrCreate([
            'id' => 2,
            'name' => 'User', 
            'slug' => 'user',
            'permissions' => [
                'manage-pegawai' => true,
            ]
        ]);
    }
}
