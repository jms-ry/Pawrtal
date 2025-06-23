<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Animal;
use Illuminate\Database\Seeder;

class AnimalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Animal::create([
        'name' => 'Bella',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'female',
        'age' => '2 years',
        'size' => 'medium',
        'color' => 'Brown and Black',
        'destinctive_features' => 'Spotted ears',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'available',
        'image' => 'images/welcome/rescue-section/dog-1.jpg'
      ]);

      Animal::create([
        'name' => 'Milo',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'male',
        'age' => '3 years',
        'size' => 'medium',
        'color' => 'White with Brown Spots',
        'destinctive_features' => 'Brown Spots on head',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => false,
        'adoption_status' => 'unavailable',
        'image' => 'images/welcome/rescue-section/dog-2.jpg'
      ]);

      Animal::create([
        'name' => 'Lucy',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'female',
        'age' => '2 years',
        'size' => 'medium',
        'color' => 'White and Brown',
        'destinctive_features' => 'White spot near the nose',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'adopted',
        'image' => 'images/welcome/rescue-section/dog-3.jpg'
      ]);

      Animal::create([
        'name' => 'Max',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'male',
        'age' => '3 years',
        'size' => 'medium',
        'color' => 'White and Black',
        'destinctive_features' => 'White line in the center of the head',
        'health_status' => 'healthy',
        'vaccination_status' => 'partially_vaccinated',
        'spayed_neutered' => false,
        'adoption_status' => 'unavailable',
        'image' => 'images/welcome/rescue-section/dog-4.jpg'
      ]);

      Animal::create([
        'name' => 'Daisy',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'female',
        'age' => '11 months',
        'size' => 'medium',
        'color' => 'Black and White',
        'destinctive_features' => 'White line in the center of its face',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'available',
        'image' => 'images/welcome/rescue-section/dog-5.jpg'
      ]);

      Animal::create([
        'name' => 'Teddy',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'male',
        'age' => '10 months',
        'size' => 'medium',
        'color' => 'Brown',
        //'destinctive_features' => 'White line in the center of its face',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'available',
        'image' => 'images/welcome/rescue-section/dog-6.jpg'
      ]);

      Animal::create([
        'name' => 'Stella',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'female',
        'age' => '1 year',
        'size' => 'medium',
        'color' => 'Brown',
        'destinctive_features' => 'Black line in the center of its face, Little bit black on the ears',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'adopted',
        'image' => 'images/welcome/rescue-section/dog-7.jpg'
      ]);

      Animal::create([
        'name' => 'Charlie',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'male',
        'age' => '2 years',
        'size' => 'medium',
        'color' => 'Brown',
        'destinctive_features' => 'White line on the nose',
        'health_status' => 'healthy',
        'vaccination_status' => 'vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'adopted',
        'image' => 'images/welcome/rescue-section/dog-8.jpg'
      ]);

      Animal::create([
        'name' => 'Willow',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'female',
        'age' => '2 years',
        'size' => 'medium',
        'color' => 'Black',
        //'destinctive_features' => 'White line on the nose',
        'health_status' => 'healthy',
        'vaccination_status' => 'partially_vaccinated',
        'spayed_neutered' => true,
        'adoption_status' => 'unavailable',
        'image' => 'images/welcome/rescue-section/dog-9.jpg'
      ]);

      Animal::create([
        'name' => 'Bear',
        'species' => 'Dog',
        'breed' => 'Aspin',
        'description' => 'A friendly and energetic dog looking for a loving home.',
        'sex' => 'male',
        'age' => '11 months',
        'size' => 'medium',
        'color' => 'Black with Brown',
        'destinctive_features' => 'A little bit of black on its head, white spot on the chest',
        'health_status' => 'healthy',
        'vaccination_status' => 'partially_vaccinated',
        'spayed_neutered' => false,
        'adoption_status' => 'unavailable',
        'image' => 'images/welcome/rescue-section/dog-10.jpg'
      ]);


    }
}
