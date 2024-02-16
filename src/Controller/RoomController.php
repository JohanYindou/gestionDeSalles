<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoomController extends AbstractController
{
    #[Route('/room/{id}', name: 'app_room')]
    public function index(
        Request $request,
        RoomRepository $roomRepository,
        EntityManagerInterface $entityManager,
    ): Response {

        // Récupérer l'utilisateur actuellement connecté
        $currentUser = $this->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$currentUser) {
            throw $this->createNotFoundException('Utilisateur non connecté');
        }

        $room = $roomRepository->findOneBy(
            ['id' => $request->attributes->get('id')],
        );
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        $booking = new Booking();
        $booking->setRoom_id($room); // Définir la salle pour la réservation
        $booking->setUserId($currentUser); // Définir l'utilisateur pour la réservation
        $booking->setStartDate(new \DateTime($request->request->get('start_date'))); // Récupérer la date de début du formulaire
        $booking->setEndDate(new \DateTime($request->request->get('end_date'))); // Récupérer la date de fin du formulaire
        $booking->setAmount($room->getPrice()); // Définir le prix de la salle
        $booking->setState(false); // Définir l'état de la reservation
        $booking->setStatus('Non payé'); // Définir le statut à "Non payé"
        $booking->setCreatedAt(new \DateTime()); // Définir la date de création à maintenant

        // Création du formulaire de réservation
        $bookingForm = $this->createForm(BookingType::class, $booking);
        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            // Traitement du formulaire si soumis et valide
            // Par exemple, enregistrement de la réservation dans la base de données
            $entityManager->persist($booking);
            $entityManager->flush();

            // Affichage du message de réservation effectuée
            $this->addFlash('success', 'Réservation effectuée avec succès !');
            return $this->redirectToRoute('app_room', ['id' => $room->getId()]);
        }


        return $this->render('room/index.html.twig', [
            'room' => $room,
            'bookingForm' => $bookingForm->createView(),
        ]);
    }


    #[Route('/rooms', name: 'app_rooms')]
    public function showAll(
        Request $request,
        RoomRepository $roomRepository,
        PaginatorInterface $paginator
    ): Response {
        $rooms = $paginator->paginate(
            $rooms = $roomRepository->findAll(),
            $request->query->getInt('page', 1),
            9

        );

        return $this->render('room/rooms.html.twig', [
            'rooms' => $rooms,
            'featuresRooms' => $roomRepository->findAll(),
        ]);
    }
}
