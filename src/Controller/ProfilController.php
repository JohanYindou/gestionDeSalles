<?php

namespace App\Controller;

use App\Entity\Booking;
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
        ProfileService $profileService,
    ): Response {
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
            'user' => $user,
        ]);
    }

    #[Route('/profil/edit', name: 'profil/edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userForm = $this->createForm(ProfilType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $user->setFirstname($user->getFirstname());
            $user->setLastname($user->getLastname());
            $user->setPhone($user->getPhone());
            $user->setAddress($user->getAddress());
            $user->setUpdatedAt(new \DateTime());
            if($userForm->get('picture')->getData()){
                $file = $userForm->get('picture')->getData();
                $originalFileName =$file->getClientOriginalName();
                $file->move(
                    $this->getParameter('upload_profilePictures'),
                    $originalFileName
                );
                $user->setPicture('uploads/users/'.$originalFileName);
            }
            $em->persist($user);
            $em->flush();



            $this->addFlash('success', "Votre profil a bien été mis à jour");
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'userForm' => $userForm->createView(),
        ]);
    }
}
