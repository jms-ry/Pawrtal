<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
  /**
    * Run the database seeds.
  */
  public function run(): void
  {
    $users = User::pluck('id')->toArray();
    
    //Lost reports
    Report::create([
      'user_id' => $users[1],
      'animal_name' => 'Bella',
      'type' => 'lost',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Brown with black',
      'sex' => 'female',
      'age_estimate' => '2 years',
      'size' => 'medium',
      'last_seen_location' => 'Location 1',
      'last_seen_date' => '2023-01-01',
      'distinctive_features' => 'Spotted ears',
      'image' => 'images/rescues/dog-1.jpg',
      'status' => 'resolved'
    ]);

    Report::create([
      'user_id' => $users[2],
      'animal_name' => 'Milo',
      'type' => 'lost',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'White with Brown Spots',
      'sex' => 'male',
      'distinctive_features' => 'Brown Spots on head',
      'age_estimate' => '3 years',
      'size' => 'medium',
      'last_seen_location' => 'Location 2',
      'last_seen_date' => '2023-03-01',
      'image' => 'images/rescues/dog-2.jpg'

    ]);

    Report::create([
      'user_id' => $users[3],
      'animal_name' => 'Lucy',
      'type' => 'lost',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'White and Brown',
      'sex' => 'female',
      'age_estimate' => '2 years',
      'size' => 'medium',
      'last_seen_location' => 'Location 3',
      'last_seen_date' => '2023-05-01',
      'distinctive_features' => 'White spot near the nose',
      'image' => 'images/rescues/dog-3.jpg',
      'status' => 'resolved'
    ]);

    Report::create([
      'user_id' => $users[2],
      'animal_name' => 'Max',
      'type' => 'lost',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'White and Black',
      'sex' => 'male',
      'distinctive_features' => 'White line in the center of the head',
      'age_estimate' => '3 years',
      'size' => 'medium',
      'last_seen_location' => 'Location 4',
      'last_seen_date' => '2023-11-01',
      'image' => 'images/rescues/dog-4.jpg'

    ]);

    Report::create([
      'user_id' => $users[3],
      'type' => 'lost',
      'animal_name' => 'Daisy',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Black and White',
      'sex' => 'female',
      'age_estimate' => '11 months',
      'size' => 'medium',
      'last_seen_location' => 'Location 5',
      'last_seen_date' => '2023-07-01',
      'distinctive_features' => 'White line in the center of its face',
      'image' => 'images/rescues/dog-5.jpg',
      'status' => 'resolved'

    ]);

    //Found reports
    Report::create([
      'user_id' => $users[2],
      'type' => 'found',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Brown',
      'sex' => 'male',
      'age_estimate' => '10 months',
      'size' => 'medium',
      'found_location' => 'Location 6',
      'found_date' => '2024-01-01',
      'condition' => 'Healthy',
      'temporary_shelter' => 'Shelter 1',
      'image' => 'images/rescues/dog-6.jpg',
      'status' => 'resolved'
    ]);

    Report::create([
      'user_id' => $users[3],
      'type' => 'found',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Brown',
      'sex' => 'female',
      'age_estimate' => '1 year',
      'size' => 'medium',
      'distinctive_features' => 'Black line in the center of its face, Little bit black on the ears',
      'found_location' => 'Location 7',
      'found_date' => '2024-01-01',
      'condition' => 'Healthy',
      'temporary_shelter' => 'Shelter 2',
      'image' => 'images/rescues/dog-7.jpg',
      
    ]);
    
    Report::create([
      'user_id' => $users[2],
      'type' => 'found',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Brown',
      'sex' => 'male',
      'age_estimate' => '2 years',
      'size' => 'medium',
      'distinctive_features' => 'White line on the nose',
      'found_location' => 'Location 8',
      'found_date' => '2024-05-01',
      'condition' => 'Injured',
      'temporary_shelter' => 'Shelter 1',
      'image' => 'images/rescues/dog-8.jpg',
      'status'=> 'resolved',
    ]);

    Report::create([
      'user_id' => $users[3],
      'type' => 'found',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Black',
      'sex' => 'female',
      'age_estimate' => '2 years',
      'size' => 'medium',
      'found_location' => 'Location 9',
      'found_date' => '2024-02-01',
      'condition' => 'Healthy',
      'temporary_shelter' => 'Shelter 2',
      'image' => 'images/rescues/dog-9.jpg',
      
    ]);

    Report::create([
      'user_id' => $users[3],
      'type' => 'found',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'color' => 'Black with Brown',
      'distinctive_features' => 'A little bit of black on its head, white spot on the chest',
      'sex' => 'male',
      'age_estimate' => '11 months',
      'size' => 'medium',
      'found_location' => 'Location 10',
      'found_date' => '2024-03-01',
      'condition' => 'Injured',
      'temporary_shelter' => 'Shelter 1',
      'image' => 'images/rescues/dog-10.jpg',
      'status'=> 'resolved',
    ]);
  }
}
