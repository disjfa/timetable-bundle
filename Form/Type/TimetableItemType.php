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
use Symfony\Component\Validator\Constraints\GreaterThan;
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
            'label' => 'form.timetable.label.description',
            'attr' => [
                'data-controller' => 'easymde',
            ],
        ]);

        $builder->add('intro', TextareaType::class, [
            'required' => false,
            'label' => 'form.timetable.label.intro',
            'attr' => [
                'data-controller' => 'easymde',
            ],
        ]);

        $dateAt = new DateTime();
        $timeTableItem = $options['data'];
        if ($timeTableItem instanceof TimetableItem) {
            $dateAt = $timeTableItem->getDate()->getDateAt();
        }
        $minDate = clone $dateAt;
        $minDate->setTime(0, 0, 0);
        $maxDate = clone $minDate;
        $maxDate->add(new \DateInterval('P1D'));

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
                'data-min-date' => $minDate->format('Y-m-d H:i'),
                'data-max-date' => $maxDate->format('Y-m-d H:i'),
            ],
        ]);

        $builder->add('dateEnd', DateType::class, [
            'label' => 'form.timetable_item.label.date_end',
            'html5' => false,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
            'constraints' => [
                new DateTime([
                    'format' => 'Y-m-d H:i',
                    'groups' => 'string',
                ]),
                new GreaterThan([
                    'propertyPath' => 'parent.all[dateStart].data',
                ]),
            ],
            'attr' => [
                'data-controller' => 'flatpickr',
                'data-format' => 'Y-m-d H:i',
                'data-min-date' => $minDate->format('Y-m-d H:i'),
                'data-max-date' => $maxDate->format('Y-m-d H:i'),
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
