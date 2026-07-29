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
      'name' => 'Scooby Doo',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Scooby Doo is a sweet and affectionate boy who loves pets and quiet afternoons. He warms up quickly to new people and enjoys the company of other dogs. Scooby Doo is housetrained and does well in a calm household. He is looking for a loving family who will give him the gentle care he deserves.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'large',
      'color' => 'Brown and White',
      'distinctive_features' => 'Has brown spots on his body and has brown ears',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/ScoobyDooProfile.jpg',
      'images'=>[
        'images/rescues/ScoobyDoo1.jpg',
        'images/rescues/ScoobyDoo1.jpg',
        'images/rescues/ScoobyDoo1.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Ungas',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Ungas is a playful and curious boy who always keeps things interesting. He loves exploring new surroundings and is always up for a game of fetch. Ungas gets along well with children and is quick to learn new tricks. He is looking for an active family who can match his energetic spirit.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'medium',
      'color' => 'Creamy White',
      'distinctive_features' => 'His nose has a little bit brown color',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/UngasProfile.jpg',
      'images'=>[
        'images/rescues/Ungas1.jpg',
        'images/rescues/Ungas2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Tiger',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Tiger is a cheerful and sociable girl who loves being around people. She has a gentle temperament and gets along well with other animals. Tiger enjoys short walks and lazy afternoons on the couch. She is looking for a family who will shower her with the love and attention she thrives on.',
      'sex' => 'female',
      'age' => '5 years old',
      'size' => 'medium',
      'color' => 'Brown with black stripes',
      'distinctive_features' => 'Black Tongue',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/TigerProfile.jpg',
      'images'=>[
        'images/rescues/Tiger1.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Aki',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Aki is a gentle dog with a heart of gold. She loves long walks in the park and belly rubs. She\'s incredibly gentle with children and gets along well with other dogs. Aki is housetrained and comes to you when her name is called. She\'s looking for a family who can give her the space to stretch her legs and the love she deserves.',
      'sex' => 'female',
      'age' => '5 years old',
      'size' => 'small',
      'color' => 'Creamy White',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/AkiProfile.jpg',
      'images'=>[        
        'images/rescues/AkiProfile.jpg',
        'images/rescues/AkiProfile.jpg',
        'images/rescues/AkiProfile.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Buta',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Buta is a gentle and easygoing boy who loves basking in the sun and going on leisurely walks. He has a calm demeanor and does wonderfully with children and other pets. Buta is housetrained and well-behaved indoors. He is looking for a peaceful home where he can live out his days in comfort and love.',
      'sex' => 'male',
      'age' => '5 years old',
      'size' => 'medium',
      'color' => 'Creamy White',
      'distinctive_features' => 'His right eye is missing',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/Buta1.jpg',
      'images'=>[
        'images/rescues/Buta1.jpg',
        'images/rescues/Buta1.jpg',
        'images/rescues/Buta1.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Daniel',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Daniel is a warm and lovable boy who lives up to his name. He loves snuggling up beside his favorite humans and is happiest when he is close to the people he loves. Daniel is well-behaved and adapts easily to new environments. He is looking for a family who enjoys quiet evenings and lots of cuddle time.',
      'sex' => 'male',
      'age' => '5 years old',
      'size' => 'large',
      'color' => 'Brown',
      'distinctive_features' => 'Black mouth',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/DanielProfile.jpg',
      'images'=>[
        'images/rescues/Daniel1.jpg',
        'images/rescues/Daniel1.jpg',
        'images/rescues/Daniel1.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Balcor',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Balcor is a confident and spirited boy who loves being the center of attention. He is intelligent, eager to please, and picks up commands quickly. Balcor enjoys outdoor adventures and loves meeting new people. He is looking for an active and loving family who can keep up with his lively personality.',
      'sex' => 'male',
      'age' => '6 years old',
      'size' => 'large',
      'color' => 'White',
      'distinctive_features' => 'Has black spots on his legs and nose',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/BalcorProfile.jpg',
      'images'=>[
        'images/rescues/Balcor1.jpg',
        'images/rescues/Balcor1.jpg',
        'images/rescues/Balcor1.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Puma',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Puma is a friendly and outgoing boy who gets along with just about everyone he meets. He loves outdoor walks and playing with toys. Puma is housetrained and responds well to positive reinforcement. He is looking for a family who will give him plenty of playtime and affection.',
      'sex' => 'male',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'Black',
      'distinctive_features' => 'His chest has a white fur',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/PumaProfile.jpg',
      'images'=>[
        'images/rescues/Puma1.jpg',
        'images/rescues/Puma2.jpg',
        'images/rescues/Puma3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Bulhog',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Bulhog is a calm and cheerful boy with a gentle soul. He enjoys quiet walks and curling up in cozy spots. Bulhof is patient and does well with children and other animals. He is looking for a serene home where he can feel safe, loved, and truly at peace.',
      'sex' => 'male',
      'age' => '4 years old',
      'size' => 'large',
      'color' => 'Black and White',
      'distinctive_features' => 'White line on his face with each side has black color. His left eye is partially blind',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/BulhogProfile.jpg',
      'images'=>[
        'images/rescues/Bulhog1.jpg',
        'images/rescues/Bulhog2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'German',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'German is a big-hearted boy who is as loyal as they come. He loves outdoor adventures and is always eager to explore new trails and open spaces. German is incredibly gentle and affectionate with the people he trusts. He is looking for an adventurous family who will make him their faithful companion.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'large',
      'color' => 'Light Brown',
      'distinctive_features' => 'Has black mouth',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => 'true',
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/GermanProfile.jpg',
      'images'=>[
        'images/rescues/German1.jpg',
        'images/rescues/German2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Esmi',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Esmi is a gentle giant with a heart of gold. She loves long walks in the park and belly rubs. Despite her size, she\'s incredibly gentle with children and gets along well with other dogs. Esmi is housetrained and knows basic commands like sit, stay, and come. She\'s looking for a family who can give him the space to stretch her legs and the love she deserves.',
      'sex' => 'female',
      'age' => '5 years old',
      'size' => 'medium',
      'color' => 'Mild Brown',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/EsmiProfile.jpg',
      'images' => [
        'images/rescues/Esmi1.jpg',
        'images/rescues/Esmi2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Tina',
      'species' => 'Dog',
      'breed' => 'Aspin Mix',
      'description' => 'Tina is a sweet and calm girl who loves nothing more than curling up on the couch for cuddles. She\'s perfect for someone looking for a low-energy companion. Tina is great with children and very patient. She enjoys leisurely walks but is equally happy lounging at home. She\'s fully housetrained and has impeccable indoor manners.',
      'sex' => 'female',
      'age' => '1 year old',
      'size' => 'small',
      'color' => 'White',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => false,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/TinaProfile.jpg',
      'images' => [
        'images/rescues/Tina1.jpg',
        'images/rescues/Tina2.jpg',
        'images/rescues/Tina3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Chloe',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Chloe is a playful and energetic pup who loves to play fetch and run around. He\'s very social and friendly with everyone he meets, including other dogs at the park. Chloe would thrive in an active household where he can get plenty of exercise. He\'s intelligent and eager to please, making him easy to train. Chloe is looking for a family that can keep up with his boundless energy.',
      'sex' => 'male',
      'age' => '9 months old',
      'size' => 'small',
      'color' => 'Black and White',
      'distinctive_features' => 'Has curly fur',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/ChloeProfile.jpg',
      'images' => [
        'images/rescues/Chloe1.jpg',
        'images/rescues/Chloe2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Bakikang',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Bakikang is a cheerful and loving dog who adores being around people. He\'s excellent with children and has a gentle, patient temperament. Bakikang enjoys moderate exercise like daily walks but is also content to relax at home. He\'s well-behaved indoors and gets along wonderfully with other pets. Bakikang is looking for a family who will shower him with love and attention.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Black',
      'distinctive_features' => 'Has a unique way of walking',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/BakikangProfile.jpg',
      'images' => [
        'images/rescues/Bakikang1.jpg',
        'images/rescues/Bakikang2.jpg',
        'images/rescues/Bakikang3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Klang-Klang',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Klang-Klang is a protective and loyal companion who bonds deeply with his family. He\'s calm and composed, making him an excellent watchdog. While he can be reserved with strangers initially, he warms up quickly once he knows you. Klang-Klang is great with older children and prefers to be the only dog in the household. He enjoys regular exercise and mental stimulation through training.',
      'sex' => 'male',
      'age' => '5 years old',
      'size' => 'large',
      'color' => 'Dirty White',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/KlangKlangProfile.jpg',
      'images' => [
        'images/rescues/KlangKlang1.jpg',
        'images/rescues/KlangKlang2.jpg',
        'images/rescues/KlangKlang3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Buni',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Buni is an affectionate lap dog who loves to be close to her humans. Despite his appearance, he has a big personality and isn\'t afraid to express his opinions with adorable barks. Buni is good with other dogs and loves being pampered.',
      'sex' => 'male',
      'age' => '1 year old',
      'size' => 'medium',
      'color' => 'Brown',
      'distinctive_features' => 'Has less furr on his body',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => false,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/BuniProfile.jpg',
      'images' => [
        'images/rescues/Buni1.jpg',
        'images/rescues/Buni2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Browny',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Browny is everyone\'s best friend. This social butterfly loves meeting new people and making friends. He\'s playful, energetic, and always ready for an adventure. Browny is great with kids and other dogs, making him perfect for an active family. He knows basic commands and is always eager to learn new tricks for treats.',
      'sex' => 'male',
      'age' => '5 years old',
      'size' => 'medium',
      'color' => 'Brown',
      'distinctive_features' => 'Has semi-erect button ears that fold forward at the tip',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/BrownyProfile.jpg',
      'images' => [
        'images/rescues/Browny1.jpg',
        'images/rescues/Browny2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Buho',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Buho is a gentle soul who loves quiet companionship. He\'s calm, well-mannered, and incredibly loving. Buho enjoys leisurely walks and peaceful afternoons in the garden. He\'s wonderful with children and has a nurturing nature. Buho would do best in a calm household where he can be a devoted companion. He\'s housetrained and has excellent house manners.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'large',
      'color' => 'Dirty Brown',
      'distinctive_features' => 'Has a big scarred wound in between his eyes',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/BuhoProfile.jpg',
      'images' => [
        'images/rescues/Buho1.jpg',
        'images/rescues/Buho2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Husky-Husky',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Husky-Husky is a strong, athletic dog who loves outdoor activities. He\'s highly energetic and would make an excellent jogging or hiking companion. Husky-Husky is intelligent and responds well to training. He\'s loyal and protective of his family while being friendly with proper introductions. Husky-Husky needs an experienced owner who can provide structure, exercise, and mental stimulation.',
      'sex' => 'male',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'Black and White',
      'distinctive_features' => 'Has white fur on the chest and a white line on the face',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/HuskyHuskyProfile.jpg',
      'images' => [
        'images/rescues/HuskyHusky1.jpg',
        'images/rescues/HuskyHusky2.jpg',
        'images/rescues/HuskyHusky3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Mida',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Mida is a sweet-natured boy who still has plenty of love to give. He\'s calm, gentle, and enjoys the simple pleasures in life like sunny spots for napping and gentle walks. Mida is perfect for someone looking for a low-maintenance, loving companion. He\'s good with other pets and wonderful with children. He\'s in excellent health and has many happy years ahead.',
      'sex' => 'male',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'White',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/MidaProfile.jpg',
      'images' => [
        'images/rescues/Mida1.jpg',
        'images/rescues/Mida2.jpg',
        'images/rescues/Mida4.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Kibol',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Kibol is a fun-loving dog with endless enthusiasm for life. He\'s curious, playful, and always ready to explore. Kibol is at the perfect age for training and will need a patient family committed to helping him learn good manners. He loves playing with toys, especially balls and ropes. Kibol gets along great with other dogs and would love a canine sibling to play with.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'medium',
      'color' => 'Black and White',
      'distinctive_features' => 'Has black spots on his body, a short tail, and a foggy eyes',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/KibolProfile.jpg',
      'images' => [
        'images/rescues/Kibol1.jpg',
        'images/rescues/Kibol2.jpg',
        'images/rescues/Kibol3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Pincher',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Pincher is an independent girl who enjoys both human company and alone time. She\'s well-balanced, not too needy but always happy to see you. Pincher is perfect for someone who works from home as she\'s content to nap nearby while you work. She enjoys moderate exercise and is well-behaved on walks. Pincher is friendly with other dogs but would also be happy as an only pet.',
      'sex' => 'female',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Brown',
      'distinctive_features' => 'Has brown spots on her body',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/PincherProfile.jpg',
      'images' => [
        'images/rescues/Pincher1.jpg',
        'images/rescues/Pincher2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Chocolate',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Chocolate is a big teddy bear who loves everyone. She\'s incredibly gentle and affectionate. Chocolate is great with children and other animals, showing remarkable patience and kindness. She enjoys leisurely walks and loves water - bath time is his favorite! Chocolate would thrive in a home with a yard where she can lounge in the sun.',
      'sex' => 'female',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'Dark Brown',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/ChocolateProfile.jpg',
      'images' => [
        'images/rescues/Chocolate1.jpg',
        'images/rescues/Chocolate2.jpg',
        'images/rescues/Chocolate3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Pulic',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Pulic is a smart and alert little dog who loves learning new things. He picks up tricks quickly and enjoys mental challenges. Pulic is very loyal to his family and can be a bit protective, making him an excellent watchdog. He needs consistent training and socialization. Pulic would do best in a home without very young children where he can be the center of attention.',
      'sex' => 'male',
      'age' => '1 year old',
      'size' => 'medium',
      'color' => 'Black and Brown',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/PulicProfile.jpg',
      'images' => [
        'images/rescues/Pulic1.jpg',
        'images/rescues/Pulic2.jpg',
        'images/rescues/Pulic3.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Tibor',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Tibor is a confident and dignified dog with a noble bearing. He\'s calm and composed, showing excellent manners both indoors and outdoors. Tibor is gentle with children and respectful of other pets. He enjoys regular exercise but isn\'t overly demanding. Tibor would make an excellent companion for someone looking for a well-mannered, loyal dog. He\'s already well-trained and knows many commands.',
      'sex' => 'male',
      'age' => '3 years old',
      'size' => 'medium',
      'color' => 'Brown',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/TiborProfile.jpg',
      'images' => [
        'images/rescues/Tibor1.jpg',
        'images/rescues/Tibor2.jpg',
        'images/rescues/Tibor3.jpg',
        'images/rescues/Tibor4.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Loki',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Loki is a loyal and watchful boy who takes his role as a companion seriously. He is alert and protective of the people he loves, yet gentle and affectionate once he warms up. Loki is well-behaved and adapts easily to new environments. He is looking for a family who will appreciate his devotion and give him a place to truly call home.',
      'sex' => 'male',
      'age' => '2 years old',
      'size' => 'medium',
      'color' => 'Dirty White',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/LokiProfile.jpg',
      'images' => [
        'images/rescues/Loki1.jpg',
        'images/rescues/Loki2.jpg',
        'images/rescues/Loki3.jpg',
        'images/rescues/Loki4.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Delfin',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Delfin is a cheerful and gentle boy who carries himself with quiet dignity. He loves soft pats and being near his favorite people. Delfin is calm indoors and enjoys short walks in the morning. He gets along well with other dogs and is especially good with children. He is looking for a family who will treat him like the royalty he is.',
      'sex' => 'male',
      'age' => '1 year old',
      'size' => 'medium',
      'color' => 'Light Brown',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/DelfinProfile.jpg',
      'images' => [
        'images/rescues/Delfin1.jpg',
        'images/rescues/Delfin2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Mini Tiger',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Mini Tiger is a mischievous and playful girl who always finds a way to make everyone around her smile. She is full of energy and loves chasing balls and exploring the yard. Mini Tiger is smart and responds well praise. She is looking for an active family who enjoys laughter and does not mind a little bit of chaos.',
      'sex' => 'female',
      'age' => '4 years old',
      'size' => 'small',
      'color' => 'Brown with black stripes',
      'distinctive_features' => 'Has a little bit of white on her chest',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/MiniTigerProfile.jpg',
      'images' => [
        'images/rescues/MiniTiger1.jpg',
        'images/rescues/MiniTiger2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Pungkol',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Pungkol is a sweet and nurturing girl who has a natural instinct to care for those around her. She is calm, patient, and wonderfully gentle with children. Pungkol enjoys lounging in shaded spots and going on leisurely afternoon walks. She is housetrained and well-mannered indoors. She is looking for a quiet and loving home where she can feel truly safe.',
      'sex' => 'female',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'White with brown',
      'distinctive_features' => 'Has a brown spots on her body and big brown spot on her back',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/PungkolProfile.jpg',
      'images' => [
        'images/rescues/Pungkol1.jpg',
        'images/rescues/Pungkol2.jpg',
      ],
    ]);

    Rescue::create([
      'name' => 'Minda',
      'species' => 'Dog',
      'breed' => 'Aspin',
      'description' => 'Minda is a unique and charming girl. She is curious and adventurous, always eager to sniff out something new. Minda is friendly with strangers and loves being the center of attention. She is housetrained and knows basic commands. She is looking for a family who will celebrate his one-of-a-kind personality.',
      'sex' => 'female',
      'age' => '4 years old',
      'size' => 'medium',
      'color' => 'Black and White',
      'distinctive_features' => 'Has semi folded ear, each side of eye and ear has black color and has little black spots on her body',
      'health_status' => 'healthy',
      'vaccination_status' => 'vaccinated',
      'spayed_neutered' => true,
      'adoption_status' => 'available',
      'profile_image' => 'images/rescues/MindaProfile.jpg',
      'images' => [
        'images/rescues/Minda1.jpg',
        'images/rescues/Minda2.jpg',
        'images/rescues/Minda3.jpg',
        'images/rescues/Minda5.jpg',
      ],
    ]);
  }
}
