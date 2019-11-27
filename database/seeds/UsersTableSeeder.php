<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
	        'username'				=> 'admin',
	        'password'              => \Illuminate\Support\Facades\Hash::make('password'),
	        'role'                  => \App\Models\User::ADMIN_ROLE,
	        'is_active'             => true,
        ]);
    }
}