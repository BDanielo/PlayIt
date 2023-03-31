<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('input', null, [
                'empty_data' => "all",
                'required' => false,
                ])
            ->add('range', RangeType::class, [
                'attr' => [
                    'id' => 'price-range',
                    'min' => 5,
                    'max' => 100,
                    'value' => 100,
                    'step' => 1,
                ],
                ])
                ->add('promotions', CheckboxType::class, [
                    'label' => 'Sales',
                    'required' => false,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
