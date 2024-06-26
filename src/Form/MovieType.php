<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('synopsis')
            ->add('releasedYear')
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('actors', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('directors', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('producers', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
