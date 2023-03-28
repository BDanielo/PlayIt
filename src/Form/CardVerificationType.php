<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\DTO\CardVerificationDTO;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CardVerificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardOwner', null, [
                'attr' => [
                    'placeholder' => 'Card Owner'
                ]
            ])
            ->add('cardNumber', null, [
                'attr' => [
                    'placeholder' => '1234 5678 9012 3456'
                ]
            ])
            ->add('cvv', TextType::class, [
                'attr' => [
                    'placeholder' => '123',
                ]
            ])
            // add a NUmberField expirationMonth with placeholder using next month month, minimum value is next month
            ->add('expirationMonth', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'placeholder' => date('m', strtotime('+1 month')),
                    'min' => date('m', strtotime('+1 month')),
                    'max' => 12
                ],
            ])
            // same with expirationYear
            ->add('expirationYear', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'placeholder' => date('y'),
                    'min' => date('y'),
                    'max' => 99
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Pay',
                'attr' => [
                    'class' => 'text-sm btn btn-primary inline-block rounded-full bg-danger px-6 pt-2.5 pb-2 font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#AE4CDC] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)] focus:outline-none focus:ring-0 active:bg-danger-700 active:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)]'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => CardVerificationDTO::class,
        ]);
    }
}
