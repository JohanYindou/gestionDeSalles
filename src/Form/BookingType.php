<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateType::class, [
                'label' => 'Date de début :',
                'required' => true,
                'attr' => [
                    'class' => 'ms-2 mb-2',
                    'min' => date('Y-m-d'),
                ],
            ])
            ->add('end_date', DateType::class, [
                'label' => 'Date de fin :',
                'required' => true,
                'attr' => [
                    'class' => 'ms-2 mb-2',
                    'min' => date('Y-m-d'),
                ],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
