<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        Request $request,
        RoomRepository $roomRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $rooms = $paginator->paginate(
        $rooms = $roomRepository->findAll(),
            $request->query->getInt('page', 1),9

            );
        return $this->render('home/index.html.twig', [
            'rooms' => $rooms,
            'featuresRooms' => $roomRepository->findAll(),
        ]);
    }
}
