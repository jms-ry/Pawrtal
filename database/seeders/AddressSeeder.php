<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
  /**
    * Run the database seeds.
  */
  public function run(): void
  {
    Address::create([
      'barangay' => 'Cagbuhangin',
      'municipality' => 'Ormoc City',
      'province' => 'Leyte',
      'zip_code' => '1234'
    ]);

    Address::create([
      'barangay' => 'Sta. Margarita',
      'municipality' => 'Hilongos',
      'province' => 'Leyte',
      'zip_code' => '6524'
    ]);

    Address::create([
      'barangay' => 'Guadalupe',
      'municipality' => 'Inopacan',
      'province' => 'Leyte',
      'zip_code' => '5679'
    ]);
  }
}
