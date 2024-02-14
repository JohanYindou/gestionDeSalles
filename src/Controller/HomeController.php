<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        RoomRepository $roomRepository
    ): Response
    {
        $rooms = $roomRepository->findAll();
        return $this->render('home/index.html.twig', [
<<<<<<< HEAD
          
        ]);
    }
    // test Route for designing the confirmation  email
    #[Route('/email', name: 'email')]
    public function email(): Response
    {
        return $this->render('registration/confirmation_email.html.twig', [
            'signedUrl' => 'https://example.com/signed-url',
            
          
=======
            'rooms' => $rooms,
>>>>>>> f94135be144b72297d91c989098705b3890aadcf
        ]);
    }
}
