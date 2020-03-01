<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = new \App\Role;
        $adminRole->name = "ADMIN";
        $adminRole->save();

        $customer = new \App\Role;
        $customer->name = "CUSTOMER";
        $customer->save();
    }
}
