<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\RoomRepository;
use App\Repository\FeaturesRepository;
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
            return $this->redirectToRoute('app_login');
            throw $this->createNotFoundException('Utilisateur non connecté');
        }

        $room = $roomRepository->findOneBy(
            ['id' => $request->attributes->get('id')],
        );

        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        // Récupérer la liste des reservations pour la salle
        $booking = new Booking();
        $bookingForm = $this->createForm(BookingType::class, $booking);
        
        // Traitement du formulaire
        $bookingForm->handleRequest($request);
        
        dump($bookingForm);

        // Traitement du formulaire si soumis et valide
        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            $booking = $bookingForm->getData();

            if($room->getStatus() != 'Disponible') 
            {
                $this->addFlash('error', 'La salle n\'est pas disponible!');
                return $this->redirectToRoute('app_room', ['id' => $room->getId()]);
            }  

            // Vérifier si la date de fin est après la date de début
            $interval = date_diff($booking->getStartDate(), $booking->getEndDate())  ;

            if ($interval->y < 0) {
                // Date de fin dans une année antérieure
                $this->addFlash('error', 'La date de fin doit être dans l\'année en cours ou une année ultérieure.');
                return $this->redirectToRoute('app_room', ['id' => $room->getId()]);
            } elseif ($interval->y == 0 && $interval->m < 0) {
                // Date de fin dans un mois antérieur
                $this->addFlash('error', 'La date de fin doit être dans le mois en cours ou un mois ultérieur.');
                return $this->redirectToRoute('app_room', ['id' => $room->getId()]);
            } elseif ($interval->y == 0 && $interval->m == 0 && $interval->d <= 0) {
                // Date de fin identique ou antérieure à la date de début
                $this->addFlash('error', 'La date de fin doit être postérieure à la date de début.');
                return $this->redirectToRoute('app_room', ['id' => $room->getId()]);
            } 
                // Date de fin valide
                $booking->setRoom_id($room); // Définir la salle pour la réservation
                $booking->setUserId($currentUser); // Définir l'utilisateur pour la réservation
                $booking->setAmount($room->getPrice()); // Définir le prix de la salle
                $booking->setState(false); // Définir l'état de la reservation
                $booking->setStatus('Non payé'); // Définir le statut à "Non payé"
                $booking->setStartDate($booking->getStartDate()); // Récupérer la date de début du formulaire
                $booking->setEndDate($booking->getEndDate()); // Récupérer la date de fin du formulaire $booking->setEndDate($booking->getStartDate()); // Récupérer la date de fin du formulaire
                $booking->setCreatedAt(new \DateTime()); // Définir la date de création à maintenant
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
        PaginatorInterface $paginator,
        FeaturesRepository $featureRepository
    ): Response {
        
        // Récupération des critères de filtrage
        $priceMin = $request->query->get('price_min');
        $priceMax = $request->query->get('price_max');
        $capacityMin = $request->query->get('capacity_min');
        $capacityMax = $request->query->get('capacity_max');
        $status = $request->query->get('status');

        // Construction de la requête filtrée avec Doctrine Query Builder
        $queryBuilder = $roomRepository->createQueryBuilder('r');

        if ($priceMin && $priceMax) {
            $queryBuilder->andWhere('r.price BETWEEN :priceMin AND :priceMax')
                ->setParameter('priceMin', $priceMin)
                ->setParameter('priceMax', $priceMax);
        }

        if ($capacityMin && $capacityMax) {
            $queryBuilder->andWhere('r.capacity BETWEEN :capacityMin AND :capacityMax')
                ->setParameter('capacityMin', $capacityMin)
                ->setParameter('capacityMax', $capacityMax);
        }

        if ($status) {
            $queryBuilder->andWhere('r.status = :status')
                ->setParameter('status', $status);
        }

        $rooms = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('room/rooms.html.twig', [
            'rooms' => $rooms,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'capacityMin' => $capacityMin,
            'capacityMax' => $capacityMax,
            'status' => $status,
        ]);
    }
}
