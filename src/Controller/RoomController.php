<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoomController extends AbstractController
{
    #[Route('/room/{id}', name: 'app_room')]
    public function index(
        Request $request,
        RoomRepository $roomRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $room = $roomRepository->findOneBy(
            ['id' => $request->attributes->get('id')],
        );
        
        $booking = new Booking();
        $bookingForm = $this->createForm(BookingType::class, $booking);
        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            if(!$this->getUser()){
                return $this->redirectToRoute('app_login');
            }
        }
        $booking = $bookingForm->getData();
        $booking->setStartDate($bookingForm->get('startDate')->getData());
        $booking->setEndDate($bookingForm->get('endDate')->getData());
        $booking->setUser($this->getUser());
        $booking->setRoom($room);

        $entityManager->persist($booking);
        $entityManager->flush();

        return $this->render('room/index.html.twig', [
            'room' => $room,
            'bookingForm' => $bookingForm->createView(),
        ]);
    }
}
