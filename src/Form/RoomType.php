<?php

namespace App\Form;

use App\Entity\Features;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('etage')
            ->add('capacity')
            ->add('price')
            ->add('address')
            ->add('city')
            ->add('country')
            ->add('status')
            ->add('created_at')
            ->add('updated_at')
            ->add('picture')
            ->add('name')
            ->add('features', EntityType::class, [
                'class' => Features::class,
'choice_label' => 'id',
'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
