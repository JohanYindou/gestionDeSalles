<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Features;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $now = new \DateTime();
        $fiveDaysFromNow = (new \DateTime())->modify('+5 days');

        $bookings = $this->entityManager->getRepository(Booking::class)
            ->createQueryBuilder('r')
            ->where('r.start_date >= :now', 'r.start_date <= :fiveDaysFromNow')
            ->setParameter('now', $now)
            ->setParameter('fiveDaysFromNow', $fiveDaysFromNow)
            ->orderBy('r.start_date', 'ASC')
            ->getQuery()
            ->getResult();
            
        return $this->render('profil/dashboard.html.twig', [
            'bookings' => $bookings,

        ]);

    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/logo.png" height="100">')
            ->setFaviconPath('/images/logo.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Features', 'fas fa-list', Features::class);
        yield MenuItem::linkToCrud('Salles', 'fas fa-home', Room::class);
        yield MenuItem::linkToCrud('Réservations', 'fas fa-calendar', Booking::class);

        yield MenuItem::linkToRoute('Retour', 'fa fa-sign-out', 'app_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }

    public function configureActions(): Actions
    {
        return Actions::new()->add(Crud::PAGE_INDEX, Action::new('show', 'View')->linkToCrudAction(Crud::PAGE_DETAIL)->setIcon('fa fa-eye'))
        ->add(Crud::PAGE_INDEX, Action::new('delete', 'Delete')->linkToCrudAction(Crud::PAGE_EDIT)->setCssClass('btn btn-danger')->setIcon('fa fa-trash'))
        ->add(Crud::PAGE_INDEX, Action::new('edit')->linkToCrudAction(Crud::PAGE_EDIT)->setIcon('fa fa-pencil'));
    }
}
