<?php

namespace App\Controller;

use App\Service\ProfileService;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProfilType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ProfileService $profileService
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $profileService->updateProfile($form, $user, $entityManager, $this->getParameter('images_directory'));
            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('profil');
        }
        
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/edit', name: 'profil/edit')]
    public function edit(Request $request, ProfileService $profileService,): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $profileService->updateProfile($form, $user);
            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('profil');
        }
        
        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
