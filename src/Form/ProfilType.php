<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null, [
                'required' => false
            ])
            ->add('lastname', null, [
                'required' => false
            ])
            ->add('picture', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Photo de profil',
            ])
            ->add('phone', null, [
                'required' => false
            ])
            ->add('adress', null, [
                'required' => false
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
