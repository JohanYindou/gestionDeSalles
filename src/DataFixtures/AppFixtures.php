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
        $picturePaths = ['/uploads/default-1.png', '/uploads/default-2.png'];
        // Set admin
        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('Admin')
            ->setLastname('Martin')
            ->setAddress($faker->address)
            ->setPassword('$2y$13$wqXiXE8U6QhYtIRJFedLA.MkNVmDzn89jVz5CBYENUOwHfAlyYNG2')
            ->setPicture($faker->randomElement($picturePaths))
            ->setPhone('06.45.45.45.45')
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
            ->setPhone('06.45.45.45.45')
            ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($visitor);
            array_push($visitors, $visitor);
        }

        // Set Features
        $features = ['lumiere du jour', 'lumiere artificiel', 'acces PMR', 'logiciel', 'equipement',];
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

        // Set rooms
        for ($i = 0; $i < 100; $i++) {
            $room = new Room();
            $room
                ->setName('Salle')
                ->setEtage('0')
                ->setCapacity(100)
                ->setAddress($faker->address)
                ->setCountry('France')
                ->setStatus('Disponible')
                ->setCity('Cergy')
                ->addFeature($faker->randomElement($featureArray))
                ->setDescription($faker->paragraphs(3, true))
                ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'))
                ->setPrice($faker->numberBetween(150, 1500))
                ->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($room);
        }

        // $booking = new Booking;
        // for ($i = 0; $i < 8; $i++) {

        // $booking
        //         ->setStartDate($faker->dateTimeBetween('now', '+1 month'))
        //         ->setEndDate($faker->dateTimeBetween('now', '+1 month'))
        //         ->setAmount('1500')
        //         ->setStatus('Payé')
        //         ->setState('Booking')
        //         ->setRoom_id()
        //         ->setUserId($faker->numberBetween(0)
        //         ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'))
        //         ->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));          

        //     $manager->persist($booking);
        // }

        $manager->flush();
    }
}
