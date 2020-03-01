<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new \App\User;
        $admin->name = "Admin";
        $admin->email = "admin@gmail.com";
        $admin->password = \Hash::make("password");
        $admin->role_id = 1;
        $admin->save();
    }
}
