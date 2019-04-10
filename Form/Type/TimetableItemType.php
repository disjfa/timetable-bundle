<?php

namespace Disjfa\TimetableBundle\Form\Type;

use Disjfa\TimetableBundle\Entity\TimetableItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimetableItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'form.timetable_item.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('dateStart', DateTimeType::class, [
            'label' => 'form.timetable_item.label.date_start',
            'constraints' => new NotBlank(),
            'minutes' => [0, 15, 30, 45],
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd HH:mm',
        ]);

        $builder->add('dateEnd', DateTimeType::class, [
            'label' => 'form.timetable_item.label.date_end',
            'constraints' => new NotBlank(),
            'minutes' => [0, 15, 30, 45],
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd HH:mm',
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimetableItem::class,
            'translation_domain' => 'timetable',
        ]);
    }
}
