<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    /*public function configureIndex(): Response
    {
        $bookingRepository = $this->container->get(BookingRepository::class);
        $bookings = $bookingRepository->findAll();

        $stats = [
            'total_bookings' => count($bookings),

            'upcoming_bookings' => count(array_filter($bookings, function (Booking $booking) {
                return $booking->getStartDate() > new \DateTime() && $booking->getEndDate() > new \DateTime();
            })),
            'cancelled_bookings' => count(array_filter($bookings, function (Booking $booking) {
                return $booking->getStatus() ===  'cancelled';
            })),
        ];

        return $this->render('profil/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }*/
    //state false prebooking et state true booking 
    //status false prebooking et status true booking



    public function onBookingCreated(Booking $booking): void
    {
        
        if ($booking->isState() === true && $booking->getStatus() === 'en cours') {
            $this->sendAlert($booking, 'en cours');
        } elseif ($booking->isState() === true && $booking->getStatus() === 'confirmée') {
            $this->sendAlert($booking, 'confirmée');
        } else {
            $this->addFlash('warning', 'Réservation annulee');
        }
    }
    
    private function sendAlert(Booking $booking, string $status): void
    {
        $message = '';

        $message =  'Une nouvelle réservation a  été ' . $status .  '. Détails de la réservation : ' .
            'Date de début : ' . $booking->getStartDate()->format('Y-m-d') . ', ' .
            'Date de fin : ' . $booking->getEndDate()->format('Y-m-d') . ', ' .

            $this->addFlash($status === 'en cours' ? 'warning' : 'success', $message);
    }

    public function validate(Request $request, EntityManagerInterface $entityManager, Booking $booking, $em): Response
    {
        $booking->setStatus('confirmée');
        $this->$entityManager->flush();

        $this->addFlash('success', 'Réservation validée avec succès');
        return $this->redirectToRoute('app_booking_index');
    }

    public function cancel(Request $request, EntityManagerInterface $entityManager, Booking $booking, $em): Response
    {
        $booking->setStatus('annulée');
        $this->$entityManager->flush();

        $this->addFlash('success', 'Réservation annulee avec succès');
        return $this->redirectToRoute('app_booking_index');
    }
}
