<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Features;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $picturePaths = ['/uploads/users/default-1.jpg', '/uploads/users/default-2.jpg'];
        // Set admin
        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('Admin')
            ->setLastname('Martin')
            ->setAddress($faker->address)
            ->setPassword('$2y$13$wqXiXE8U6QhYtIRJFedLA.MkNVmDzn89jVz5CBYENUOwHfAlyYNG2')
            ->setPicture($faker->randomElement($picturePaths))
            ->setPhone($faker->phoneNumber)
            ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
        $manager->persist($admin);

        // Set visitor
        $visitors = [];
        for ($i = 0; $i < 8; $i++) {
            $visitor = new User();
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $email = strtolower($firstname . '.' . $lastname . '@visitor.fr');
            $visitor->setEmail($email)
            ->setRoles(['ROLE_USER'])
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setAddress($faker->address)
            ->setPicture($faker->randomElement($picturePaths))
            ->setPassword('$2y$13$wqXiXE8U6QhYtIRJFedLA.MkNVmDzn89jVz5CBYENUOwHfAlyYNG2')
            ->setPhone($faker->phoneNumber)
            ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($visitor);
            array_push($visitors, $visitor);
        }

        // Set Features

        //Ergonomie
        $features = ['lumiere du jour', 'lumiere artificiel', 'acces PMR'];
        $featureArray = [];
        for ($i = 0; $i < count($features); $i++) {
            $feature = new Features();
            $feature->setName($features[$i]);
            $feature->setDescription($faker->sentence(30));
            $feature->setType($features[$i]);
            $feature->setState($faker->boolean);
            $feature->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $feature->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($feature);
            array_push($featureArray, $feature);
        }

        // Logiciels
        $logiciels = ['VsCode', 'Pack Office', 'Pack Adobe Creative Cloud'];
        $type = ['logiciel', 'equipement'];      
        for ($i = 0; $i < count($logiciels); $i++) {
            $logiciel = new Features();
            $logiciel->setName($logiciels[$i]);
            $logiciel->setDescription($faker->sentence(30));
            $logiciel->setType($type[0]);
            $logiciel->setState($faker->boolean);
            $logiciel->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $logiciel->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($logiciel);
            array_push($featureArray, $logiciel);
        }

        // Materiels
        $materiels = ['PC', 'Tableau', 'Projecteur', 'Caméra', 'Internet'];
        for ($i = 0; $i < count($materiels); $i++) {
            $materiel = new Features();
            $materiel->setName($materiels[$i]);
            $materiel->setDescription($faker->sentence(30));
            $materiel->setType($type[1]);
            $materiel->setState($faker->boolean);
            $materiel->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $materiel->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($materiel);
            array_push($featureArray, $materiel);
        }

        // Set rooms
        $rooms = []; // Variable pour stocker les chambres

        for ($i = 0; $i < 100; $i++) {
            $room = new Room();
            $words = $faker->words($nb = 3, $asText = false);
            $words[0] = ucfirst($words[0]); // Capitalize the first word

            $room
                ->setName(implode(' ', $words))
                ->setEtage($faker->numberBetween(-1, 5))
                ->setCapacity($faker->numberBetween(1, 100))
                ->setAddress($faker->streetAddress)
                ->setCountry($faker->country())
                ->setStatus('Disponible')
                ->setCity($faker->city())
                ->setDescription($faker->paragraphs(3, true))
                ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'))
                ->setPrice($faker->numberBetween(150, 1500))
                ->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));

            shuffle($featureArray); // Mélanger le tableau des features
            $numFeatures = $faker->numberBetween(1, count($featureArray)); // Nombre aléatoire de features à ajouter
            $selectedFeatures = array_slice($featureArray, 0, $numFeatures); // Sélectionner des features aléatoires
            foreach ($selectedFeatures as $feature) {
                $room->addFeature($feature);
            }
            $manager->persist($room);
            array_push($rooms, $room); // Ajouter la chambre au tableau $rooms
        }


        $status= ['Non Payé', 'Payé'];
        // Créer des booking
        foreach ($visitors as $visitor) {
            for ($i = 0; $i < 3; $i++) {
                $booking = new Booking();
                $startDate = $faker->dateTimeBetween('now', '+1 month');
                $endDate = clone $startDate;
                $endDate->modify("+1 week");

                // Récupérer une chambre aléatoire parmi les chambres disponibles
                $randomRoom = $rooms[array_rand($rooms)];

                $booking->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setAmount($faker->numberBetween(150, 1500))
                    ->setState($faker->boolean)
                    // Si le state est à false, le booking est non payé sinon il aléatoire entre payé et non payé 
                    ->setStatus($booking->isState() ? $status[array_rand($status)] : 'Non Payé')
                    ->setRoom_id($randomRoom)
                    ->setUserId($visitor)
                    ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));

                $manager->persist($booking);
            }
        }
        $manager->flush(); // Enregistrer les réservations dans la base de données
    }
}
