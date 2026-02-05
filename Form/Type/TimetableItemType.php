<?php

namespace Disjfa\TimetableBundle\Form\Type;

use Disjfa\TimetableBundle\Entity\TimetableItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimetableItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'form.timetable_item.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('description', TextareaType::class, [
            'required' => false,
            'label' => 'form.timetable.label.about',
            'attr' => [
                'data-controller' => 'easymde',
            ],
        ]);

        $builder->add('dateStart', DateType::class, [
            'label' => 'form.timetable_item.label.date_start',
            'html5' => false,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
            'constraints' => new DateTime([
                'format' => 'Y-m-d H:i',
                'groups' => 'string',
            ]),
            'attr' => [
                'data-controller' => 'flatpickr',
                'data-format' => 'Y-m-d H:i',
            ],
        ]);

        $builder->add('dateEnd', DateType::class, [
            'label' => 'form.timetable_item.label.date_end',
            'html5' => false,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
            'constraints' => new DateTime([
                'format' => 'Y-m-d H:i',
                'groups' => 'string',
            ]),
            'attr' => [
                'data-controller' => 'flatpickr',
                'data-format' => 'Y-m-d H:i',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimetableItem::class,
            'translation_domain' => 'timetable',
        ]);
    }
}
