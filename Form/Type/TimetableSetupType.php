<?php

namespace Disjfa\TimetableBundle\Form\Type;

use Disjfa\TimetableBundle\Entity\Timetable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimetableSetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dates', CollectionType::class, [
            'entry_type' => TimetableDateType::class,
            'entry_options' => ['label' => false, 'row_attr' => ['class' => 'border p-3 mb-3 bg-light rounded']],
            'allow_add' => true,
            'by_reference' => false,
            'constraints' => [new Count(min: 1)],
            'attr' => ['data-collectiontype-target' => 'prototype'],
        ]);

        $builder->add('places', CollectionType::class, [
            'entry_type' => TimetablePlaceType::class,
            'entry_options' => ['label' => false, 'row_attr' => ['class' => 'border p-3 mb-3 bg-light rounded']],
            'allow_add' => true,
            'by_reference' => false,
            'constraints' => [new NotBlank(), new Count(min: 1)],
            'attr' => ['data-collectiontype-target' => 'prototype'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Timetable::class,
            'translation_domain' => 'timetable',
        ]);
    }
}
