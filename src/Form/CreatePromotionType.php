<?php

namespace App\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\DTO\CreatePromotionDTO;

class CreatePromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('percent', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'value' => 0,
                ],
            ])
            ->add('start_date', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('end_date', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => CreatePromotionDTO::class,
        ]);
    }
}
