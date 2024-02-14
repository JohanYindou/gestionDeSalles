<?php

namespace App\Controller;

use App\Service\ProfileService;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProfilType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ProfilType $form,
        ProfileService $profileService,
    ): Response
    {
        $form = $this->createForm(ProfilType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profileService->updateProfile($form, $this->getUser(), $entityManager, $this->getParameter('images_directory'));
            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('profil');
        }
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
