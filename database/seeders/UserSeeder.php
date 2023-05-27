<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email'=>'admin@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $admin -> assignRole('admin');

        $superadmin = User::updateOrCreate([
            'email' => 'superadmin@gmail.com'
        ], [
            'first_name' => 'superAdmin',
            'last_name' => 'superadmin',
            'email'=>'superadmin@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $superadmin -> assignRole('superadmin');

        $inventory = User::updateOrCreate([
            'email' => 'inventory@gmail.com'
        ], [
            'first_name' => 'Inventory',
            'last_name' => 'Inventory',
            'email'=>'inventory@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $inventory -> assignRole('inventory');

        $cashier = User::updateOrCreate([
            'email' => 'cashier@gmail.com'
        ], [
            'first_name' => 'cashier',
            'last_name' => 'cashier',
            'email'=>'cashier@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $cashier -> assignRole('cashier');

    }
}
