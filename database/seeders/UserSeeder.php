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
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin->assignRole($adminRole);

        $superadmin = User::updateOrCreate([
            'email' => 'superadmin@gmail.com'
        ], [
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $superadminRole = Role::where('name', 'superadmin')->first();
        $superadmin->assignRole($superadminRole);

        $inventory = User::updateOrCreate([
            'email' => 'inventory@gmail.com'
        ], [
            'name' => 'Inventory',
            'username' => 'inventory',
            'email' => 'inventory@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $inventoryRole = Role::where('name', 'inventory')->first();
        $inventory->assignRole($inventoryRole);

        $cashier = User::updateOrCreate([
            'email' => 'cashier@gmail.com'
        ], [
            'name' => 'Cashier',
            'username' => 'cashier',
            'email' => 'cashier@gmail.com',
            'password' => bcrypt('admin123')
        ]);

        $cashierRole = Role::where('name', 'cashier')->first();
        $cashier->assignRole($cashierRole);
    }
}
