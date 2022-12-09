<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            [
                'id'        => '1',
                'username'  => 'superadmin',
                'email'     => 'superadmin@gmail.com',
            ],
            [
                'password'  => bcrypt('12345'),
                'name'      => 'Super Admin'
               
            ]
        )->roles()->attach(1);
    }
}
