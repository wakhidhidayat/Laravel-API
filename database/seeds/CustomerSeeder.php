<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer1 = new \App\User;
        $customer1->name = "Alana";
        $customer1->email = "alana@gmail.com";
        $customer1->password = \Hash::make("password");
        $customer1->role_id = 2;
        $customer1->save();


}
