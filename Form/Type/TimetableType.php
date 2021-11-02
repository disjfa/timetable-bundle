<?php

namespace Disjfa\TimetableBundle\Form\Type;

use Disjfa\TimetableBundle\Entity\Timetable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimetableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'form.timetable.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('bodyBg', ColorType::class, [
            'label' => 'form.timetable.label.bodyBg',
        ]);

        $builder->add('headerBg', ColorType::class, [
            'label' => 'form.timetable.label.headerBg',
        ]);

        $builder->add('boxBg', ColorType::class, [
            'label' => 'form.timetable.label.boxBg',
        ]);

        $builder->add('side', ChoiceType::class, [
            'choices' => [
                'horizontal' => 'horizontal',
                'vertical' => 'vertical',
            ],
            'expanded' => true,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Timetable::class,
            'translation_domain' => 'timetable',
        ]);
    }
}
