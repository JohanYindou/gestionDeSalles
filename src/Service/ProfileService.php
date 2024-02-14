<?php

namespace App\Service;


class ProfileService
{
    public function updateProfile($form, $user, $em, $target)
    {
        $user->setFirstname($form->get('firstname')->getData());

        // Upload image
        if ($form->get('picture')->getData()) {
            $file = $form->get('Picture')->getData();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($target, $filename);
            $user->setPicture($filename);
        } else {
            if ($user->getPicture() == null) {
                $user->setPicture('default.png');
            } else {
                $user->setPicture($user->getImage());
            }
        }
        $em->persist($user);
        $em->flush();
    }
}
