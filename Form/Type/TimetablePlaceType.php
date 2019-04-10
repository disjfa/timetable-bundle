<?php

namespace Disjfa\TimetableBundle\Form\Type;

use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimetablePlaceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'form.timetable_place.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('seqnr', NumberType::class, [
            'label' => 'form.timetable_place.label.seqnr',
            'constraints' => new NotBlank(),
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimetablePlace::class,
            'translation_domain' => 'timetable',
        ]);
    }
}
