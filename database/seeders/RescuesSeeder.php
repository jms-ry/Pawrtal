<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Rescue;
use Illuminate\Database\Seeder;

class RescuesSeeder extends Seeder
{
  /**
    * Run the database seeds.
  */
  public function run(): void
  {
    Rescue::create([
      'name' => 'Bella',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'female',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Brown and Black',
      'distinctive_features' => 'Spotted ears',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/dog-1.jpg',
      'images'=>[
        'images/rescues/dog-1.jpg',
        'images/rescues/dog-1.jpg',
        'images/rescues/dog-1.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Milo',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'medium',
      'color' => 'White with Brown Spots',
      'distinctive_features' => 'Brown Spots on head',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => false,
      'adoption_status' => 'unavailable',
      'profile_image' => 'images/rescues/dog-2.jpg',
      'images'=>[
        'images/rescues/dog-2.jpg',
        'images/rescues/dog-2.jpg',
        'images/rescues/dog-2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Lucy',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'female',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'White and Brown',
      'distinctive_features' => 'White spot near the nose',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'adopted',
      'profile_image' => 'images/rescues/dog-3.jpg',
      'images'=>[
        'images/rescues/dog-3.jpg',
        'images/rescues/dog-3.jpg',
        'images/rescues/dog-3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Max',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'medium',
      'color' => 'White and Black',
      'distinctive_features' => 'White line in the center of the head',
      'health_status' => 'healthy',
      'vaccination_status' => 'partially_vaccinated',
      'spayed_neutered' => false,
      'adoption_status' => 'unavailable',
      'profile_image' => 'images/rescues/dog-4.jpg',
      'images'=>[
        'images/rescues/dog-4.jpg',
        'images/rescues/dog-4.jpg',
        'images/rescues/dog-4.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Daisy',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'female',
      'age' => '11 months old',
      'size' => 'medium',
      'color' => 'Black and White',
      'distinctive_features' => 'White line in the center of its face',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/dog-5.jpg',
      'images'=>[
        'images/rescues/dog-5.jpg',
        'images/rescues/dog-5.jpg',
        'images/rescues/dog-5.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Teddy',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'male',
      'age' => '10 months old',
      'size' => 'medium',
      'color' => 'Brown',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/dog-6.jpg',
      'images'=>[
        'images/rescues/dog-6.jpg',
        'images/rescues/dog-6.jpg',
        'images/rescues/dog-6.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Stella',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'female',
      'age' => '1 years old',
      'size' => 'medium',
      'color' => 'Brown',
      'distinctive_features' => 'Black line in the center of its face, Little bit black on the ears',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'adopted',
      'profile_image' => 'images/rescues/dog-7.jpg',
      'images'=>[
        'images/rescues/dog-7.jpg',
        'images/rescues/dog-7.jpg',
        'images/rescues/dog-7.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Charlie',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Brown',
      'distinctive_features' => 'White line on the nose',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'adopted',
      'profile_image' => 'images/rescues/dog-8.jpg',
      'images'=>[
        'images/rescues/dog-8.jpg',
        'images/rescues/dog-8.jpg',
        'images/rescues/dog-8.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Willow',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'female',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Black',
      'health_status' => 'healthy',
      'vaccination_status' => 'partially_vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'unavailable',
      'profile_image' => 'images/rescues/dog-9.jpg',
      'images'=>[
        'images/rescues/dog-9.jpg',
        'images/rescues/dog-9.jpg',
        'images/rescues/dog-9.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Bear',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'A friendly and energetic dog looking for a loving home.',
      'sex' => 'male',
      'age' => '11 months old',
      'size' => 'medium',
      'color' => 'Black with Brown',
      'distinctive_features' => 'A little bit of black on its head, white spot on the chest',
      'health_status' => 'healthy',
      'vaccination_status' => 'partially_vaccinated',
      'spayed_neutered' => false,
      'adoption_status' => 'unavailable',
      'profile_image' => 'images/rescues/dog-10.jpg',
      'images'=>[
        'images/rescues/dog-10.jpg',
        'images/rescues/dog-10.jpg',
        'images/rescues/dog-10.jpg',
      ],
    ]);

    // New Seed for Recommendation Testing
    // Seed 1 - Max
    Rescue::create([
      'name' => 'Max',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Max is a gentle giant with a heart of gold. He loves long walks in the park and belly rubs. Despite his size, he\'s incredibly gentle with children and gets along well with other dogs. Max is housetrained and knows basic commands like sit, stay, and come. He\'s looking for a family who can give him the space to stretch his legs and the love he deserves.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'large',
      'color' => 'Golden Brown',
      'distinctive_features' => 'White chest patch',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb',
      'images' => [
        'https://images.unsplash.com/photo-1587300003388-59208cc962cb',
        'https://images.unsplash.com/photo-1587300003388-59208cc962cb',
        'https://images.unsplash.com/photo-1587300003388-59208cc962cb',
      ],
    ]);

    // Seed 2 - Luna
    Rescue::create([
      'name' => 'Luna',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Luna is a sweet and calm girl who loves nothing more than curling up on the couch for cuddles. She\'s perfect for someone looking for a low-energy companion. Luna is great with children and very patient. She enjoys leisurely walks but is equally happy lounging at home. She\'s fully housetrained and has impeccable indoor manners.',
      'sex' => 'female',
      'age' => '5 years old',
      'size' => 'small',
      'color' => 'White and Tan',
      'distinctive_features' => 'Floppy left ear',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1',
      'images' => [
        'https://images.unsplash.com/photo-1543466835-00a7907e9de1',
        'https://images.unsplash.com/photo-1543466835-00a7907e9de1',
        'https://images.unsplash.com/photo-1543466835-00a7907e9de1',
      ],
    ]);

    // Seed 3 - Rocky
    Rescue::create([
      'name' => 'Rocky',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Rocky is a playful and energetic pup who loves to play fetch and run around. He\'s very social and friendly with everyone he meets, including other dogs at the park. Rocky would thrive in an active household where he can get plenty of exercise. He\'s intelligent and eager to please, making him easy to train. Rocky is looking for a family that can keep up with his boundless energy.',
      'sex' => 'male',
      'age' => '1 year old',
      'size' => 'medium',
      'color' => 'Black and White',
      'distinctive_features' => 'Black mask on face',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e',
      'images' => [
        'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e',
        'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e',
        'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e',
      ],
    ]);

    // Seed 4 - Daisy
    Rescue::create([
      'name' => 'Daisy',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Daisy is a cheerful and loving dog who adores being around people. She\'s excellent with children and has a gentle, patient temperament. Daisy enjoys moderate exercise like daily walks but is also content to relax at home. She\'s well-behaved indoors and gets along wonderfully with other pets. Daisy is looking for a family who will shower her with love and attention.',
      'sex' => 'female',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'Light Brown',
      'distinctive_features' => 'Curly tail',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48',
      'images' => [
        'https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48',
        'https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48',
        'https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48',
      ],
    ]);

    // Seed 5 - Charlie
    Rescue::create([
      'name' => 'Charlie',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Charlie is a protective and loyal companion who bonds deeply with his family. He\'s calm and composed, making him an excellent watchdog. While he can be reserved with strangers initially, he warms up quickly once he knows you. Charlie is great with older children and prefers to be the only dog in the household. He enjoys regular exercise and mental stimulation through training.',
      'sex' => 'male',
      'age' => '6 years old',
      'size' => 'large',
      'color' => 'Dark Brown',
      'distinctive_features' => 'Graying muzzle',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b',
      'images' => [
        'https://images.unsplash.com/photo-1548199973-03cce0bbc87b',
        'https://images.unsplash.com/photo-1548199973-03cce0bbc87b',
        'https://images.unsplash.com/photo-1548199973-03cce0bbc87b',
      ],
    ]);

    // Seed 6 - Molly
    Rescue::create([
      'name' => 'Molly',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Molly is an affectionate lap dog who loves to cuddle and be close to her humans. Despite her small size, she has a big personality and isn\'t afraid to express her opinions with adorable barks. She\'s perfect for apartment living and doesn\'t require much exercise beyond short daily walks. Molly is good with other small dogs and loves being pampered.',
      'sex' => 'female',
      'age' => '7 years old',
      'size' => 'small',
      'color' => 'Cream',
      'distinctive_features' => 'Button nose',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1561037404-61cd46aa615b',
      'images' => [
        'https://images.unsplash.com/photo-1561037404-61cd46aa615b',
        'https://images.unsplash.com/photo-1561037404-61cd46aa615b',
        'https://images.unsplash.com/photo-1561037404-61cd46aa615b',
      ],
    ]);

    // Seed 7 - Buddy
    Rescue::create([
      'name' => 'Buddy',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Buddy lives up to his name - he truly is everyone\'s best friend. This social butterfly loves meeting new people and making friends at the dog park. He\'s playful, energetic, and always ready for an adventure. Buddy is great with kids and other dogs, making him perfect for an active family. He knows basic commands and is always eager to learn new tricks for treats.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Tri-color',
      'distinctive_features' => 'Mismatched eyes',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1558788353-f76d92427f16',
      'images' => [
        'https://images.unsplash.com/photo-1558788353-f76d92427f16',
        'https://images.unsplash.com/photo-1558788353-f76d92427f16',
        'https://images.unsplash.com/photo-1558788353-f76d92427f16',
      ],
    ]);

    // Seed 8 - Sophie
    Rescue::create([
      'name' => 'Sophie',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Sophie is a gentle soul who loves quiet companionship. She\'s calm, well-mannered, and incredibly loving. Sophie enjoys leisurely walks and peaceful afternoons in the garden. She\'s wonderful with children and has a nurturing nature. Sophie would do best in a calm household where she can be a devoted companion. She\'s housetrained and has excellent house manners.',
      'sex' => 'female',
      'age' => '5 years old',
      'size' => 'small',
      'color' => 'Gray and White',
      'distinctive_features' => 'Fluffy coat',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1534361960057-19889db9621e',
      'images' => [
        'https://images.unsplash.com/photo-1534361960057-19889db9621e',
        'https://images.unsplash.com/photo-1534361960057-19889db9621e',
        'https://images.unsplash.com/photo-1534361960057-19889db9621e',
      ],
    ]);

    // Seed 9 - Duke
    Rescue::create([
      'name' => 'Duke',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Duke is a strong, athletic dog who loves outdoor activities. He\'s highly energetic and would make an excellent jogging or hiking companion. Duke is intelligent and responds well to training. He\'s loyal and protective of his family while being friendly with proper introductions. Duke needs an experienced owner who can provide structure, exercise, and mental stimulation.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'large',
      'color' => 'Brindle',
      'distinctive_features' => 'Athletic build',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1576201836106-db1758fd1c97',
      'images' => [
        'https://images.unsplash.com/photo-1576201836106-db1758fd1c97',
        'https://images.unsplash.com/photo-1576201836106-db1758fd1c97',
        'https://images.unsplash.com/photo-1576201836106-db1758fd1c97',
      ],
    ]);

    // Seed 10 - Rosie
    Rescue::create([
      'name' => 'Rosie',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Rosie is a sweet-natured senior who still has plenty of love to give. She\'s calm, gentle, and enjoys the simple pleasures in life like sunny spots for napping and gentle walks. Rosie is perfect for someone looking for a low-maintenance, loving companion. She\'s good with other pets and wonderful with children. Despite her age, she\'s in excellent health and has many happy years ahead.',
      'sex' => 'female',
      'age' => '8 years old',
      'size' => 'medium',
      'color' => 'Red',
      'distinctive_features' => 'Gentle eyes',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1552053831-71594a27632d',
      'images' => [
        'https://images.unsplash.com/photo-1552053831-71594a27632d',
        'https://images.unsplash.com/photo-1552053831-71594a27632d',
        'https://images.unsplash.com/photo-1552053831-71594a27632d',
      ],
    ]);

    // Seed 11 - Cooper
    Rescue::create([
      'name' => 'Cooper',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Cooper is a fun-loving puppy with endless enthusiasm for life. He\'s curious, playful, and always ready to explore. Cooper is at the perfect age for training and will need a patient family committed to helping him learn good manners. He loves playing with toys, especially balls and ropes. Cooper gets along great with other dogs and would love a canine sibling to play with.',
      'sex' => 'male',
      'age' => '6 months old',
      'size' => 'medium',
      'color' => 'Yellow',
      'distinctive_features' => 'Big paws',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1600077106724-946750eeaf3c',
      'images' => [
        'https://images.unsplash.com/photo-1600077106724-946750eeaf3c',
        'https://images.unsplash.com/photo-1600077106724-946750eeaf3c',
        'https://images.unsplash.com/photo-1600077106724-946750eeaf3c',
      ],
    ]);

    // Seed 12 - Sadie
    Rescue::create([
      'name' => 'Sadie',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Sadie is an independent girl who enjoys both human company and alone time. She\'s well-balanced, not too needy but always happy to see you. Sadie is perfect for someone who works from home as she\'s content to nap nearby while you work. She enjoys moderate exercise and is well-behaved on walks. Sadie is friendly with other dogs but would also be happy as an only pet.',
      'sex' => 'female',
      'age' => '4 years old',
      'size' => 'small',
      'color' => 'Black',
      'distinctive_features' => 'White sock on one paw',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1',
      'images' => [
        'https://images.unsplash.com/photo-1601758228041-f3b2795255f1',
        'https://images.unsplash.com/photo-1601758228041-f3b2795255f1',
        'https://images.unsplash.com/photo-1601758228041-f3b2795255f1',
      ],
    ]);

    // Seed 13 - Bear
    Rescue::create([
      'name' => 'Bear',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Bear is a big teddy bear who loves everyone. Despite his imposing size, he\'s incredibly gentle and affectionate. Bear is great with children and other animals, showing remarkable patience and kindness. He enjoys leisurely walks and loves water - bath time is his favorite! Bear would thrive in a home with a yard where he can lounge in the sun. He\'s a calm, loving giant.',
      'sex' => 'male',
      'age' => '5 years old',
      'size' => 'large',
      'color' => 'Black and Tan',
      'distinctive_features' => 'Fluffy coat',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1568572933382-74d440642117',
      'images' => [
        'https://images.unsplash.com/photo-1568572933382-74d440642117',
        'https://images.unsplash.com/photo-1568572933382-74d440642117',
        'https://images.unsplash.com/photo-1568572933382-74d440642117',
      ],
    ]);

    // Seed 14 - Lucy
    Rescue::create([
      'name' => 'Lucy',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Lucy is a smart and alert little dog who loves learning new things. She picks up tricks quickly and enjoys mental challenges. Lucy is very loyal to her family and can be a bit protective, making her an excellent watchdog despite her small size. She needs consistent training and socialization. Lucy would do best in a home without very young children where she can be the center of attention.',
      'sex' => 'female',
      'age' => '3 years old',
      'size' => 'small',
      'color' => 'Brown',
      'distinctive_features' => 'Perky ears',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee',
      'images' => [
        'https://images.unsplash.com/photo-1583337130417-3346a1be7dee',
        'https://images.unsplash.com/photo-1583337130417-3346a1be7dee',
        'https://images.unsplash.com/photo-1583337130417-3346a1be7dee',
      ],
    ]);

    // Seed 15 - Zeus
    Rescue::create([
      'name' => 'Zeus',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Zeus is a confident and dignified dog with a noble bearing. He\'s calm and composed, showing excellent manners both indoors and outdoors. Zeus is gentle with children and respectful of other pets. He enjoys regular exercise but isn\'t overly demanding. Zeus would make an excellent companion for someone looking for a well-mannered, loyal dog. He\'s already well-trained and knows many commands.',
      'sex' => 'male',
      'age' => '4 years old',
      'size' => 'large',
      'color' => 'Gray',
      'distinctive_features' => 'Regal posture',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'https://images.unsplash.com/photo-1598133893773-de3574464ef0',
      'images' => [
        'https://images.unsplash.com/photo-1598133893773-de3574464ef0',
        'https://images.unsplash.com/photo-1598133893773-de3574464ef0',
        'https://images.unsplash.com/photo-1598133893773-de3574464ef0',
      ],
    ]);

  }
}
