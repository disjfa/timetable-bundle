<?php

namespace Disjfa\TimetableBundle\Form\Type;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimetableDateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'form.timetable_date.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('dateAt', DateType::class, [
            'label' => 'form.timetable_date.label.dateAt',
            'constraints' => new NotBlank(),
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimetableDate::class,
            'translation_domain' => 'timetable',
        ]);
    }
}
