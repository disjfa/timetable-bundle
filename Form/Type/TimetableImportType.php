<?php

declare(strict_types=1);

namespace Disjfa\TimetableBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class TimetableImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => 'Excel file (.xlsx)',
            'constraints' => [
                new NotNull(message: 'Please upload a file.'),
                new File(
                    extensions: ['xlsx'],
                    extensionsMessage: 'Only .xlsx files are allowed.',
                ),
            ],
        ]);
    }
}
