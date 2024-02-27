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
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/index.html.twig', [
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
        }elseif ( $userForm->isSubmitted() ) {
            $this->addFlash('error', "Une erreur est survenue");
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'userForm' => $userForm->createView(),
        ]);
    }
}
