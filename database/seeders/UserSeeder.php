<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
    * Run the database seeds.
  */
  public function run(): void
  {
    $addresses = Address::pluck('id')->toArray();
    User::create(attributes:[
      'first_name' => 'Admin',
      'last_name' => 'User',
      'email' => 'admin@example.com',
      'password' => bcrypt('password'),
      'contact_number' => '1234567890',
      'role' => 'admin',
      'address_id' =>$addresses[0],
    ]);

    User::create(attributes:[
      'first_name' => 'Staff',
      'last_name' => 'User',
      'email' => 'staff@example.com',
      'password' => bcrypt('password'),
      'contact_number' => '1235476543',
      'role' => 'staff',
      'address_id' =>$addresses[1],
    ]);

    User::create(attributes:[
      'first_name' => 'Regular',
      'last_name' => 'User',
      'email' => 'regular@example.com',
      'password' => bcrypt('password'),
      'contact_number' => '1234567890',
      'role' => 'regular_user',
      'address_id' =>$addresses[2],
    ]);

    User::create(attributes:[
      'first_name' => 'Guest',
      'last_name' => 'User',
      'email' => 'guest@example.com',
      'password' => bcrypt('password'),
      'contact_number' => '1234567890',
      'role' => 'regular_user',
      'address_id' =>$addresses[2],
    ]);
  }
}
