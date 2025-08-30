<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
  /**
    * Run the database seeds.
  */
  public function run(): void
  {
    $users = User::pluck('id')->toArray();
    Address::create([
      'barangay' => 'Cagbuhangin',
      'municipality' => 'Ormoc City',
      'province' => 'Leyte',
      'zip_code' => '1234',
      'user_id' => $users[0]
    ]);

    Address::create([
      'barangay' => 'Sta. Margarita',
      'municipality' => 'Hilongos',
      'province' => 'Leyte',
      'zip_code' => '6524',
      'user_id' => $users[1]
    ]);

    Address::create([
      'barangay' => 'Guadalupe',
      'municipality' => 'Inopacan',
      'province' => 'Leyte',
      'zip_code' => '5679',
      'user_id' => $users[2]
    ]);

    Address::create([
      'barangay' => 'Plaridel',
      'municipality' => 'Baybay City',
      'province' => 'Leyte',
      'zip_code' => '5679',
      'user_id' => $users[3]
    ]);
  }
}
