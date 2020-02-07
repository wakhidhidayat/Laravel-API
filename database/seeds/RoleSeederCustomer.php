<?php

use Illuminate\Database\Seeder;

class RoleSeederCustomer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = new \App\Role;
        $customer->name = "CUSTOMER";
        $customer->save();
    }
}
