<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\Option;
use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Set admin
        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('Admin')
            ->setLastname('Martin')
            ->setAdress($faker->adress)
            ->setPassword('$2y$13$wqXiXE8U6QhYtIRJFedLA.MkNVmDzn89jVz5CBYENUOwHfAlyYNG2')
            ->setPicture('logo.png')
            ->setPhone('06.45.45.45.45')
            ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
        $manager->persist($admin);

        // Set visitor
        $visitor = [];
        for ($i = 0; $i < 8; $i++) {
            $visitor = new User();
            $visitor->setEmail('visitor' . $i . '@visitor.fr')
                ->setRoles(['ROLE_USER'])
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setAdress($faker->adress)
                ->setPassword('$2y$13$wqXiXE8U6QhYtIRJFedLA.MkNVmDzn89jVz5CBYENUOwHfAlyYNG2')
                ->setPicture('logo.png')
                ->setPhone('06.45.45.45.45')
                ->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($visitor);
            array_push($visitors, $visitor);
        }

        // Set options
        $options = ['lumière du jour', 'lumière artificiel', 'accès PMR', 'logiciel', 'équipement',];
        $optionArray = [];
        for ($i = 0; $i < count($options); $i++) {
            $option = new Option();
            $option->setName($options[$i]);
            $option->setDescription($options[$i]);
            $option->setType($options[$i]);
            $option->setState($options[$i]);
            $option->setCreatedAt($faker->dateTimeBetween('now', '+1 month'));
            $option->setUpdatedAt($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($option);
            array_push($optionArray, $option);
        }

        // Set rooms
        $room = new Room();
        for ($i = 0; $i < 100; $i++) {

            $room
                ->setName('Salle')
                ->setEtage('0')
                ->setCapacity('100')
                ->setAddress($faker->address)
                ->setCountry('France')
                ->setStatus('Disponible')
                ->setCity('Cergy')
                ->setPicture('logo.png')
                ->addOption($faker->randomElement($optionArray))
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
