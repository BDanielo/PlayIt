<?php

namespace App\Form;

use App\Entity\Coupon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\DTO\CreateCouponDTO;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CreateCouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('percent', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ])
            ->add('startDate', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'data' => new \DateTime(),
            ])
            ->add('endDate', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'data' => new \DateTime('+1 day'),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn inline-block rounded-full bg-electric-purple px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#C47BE6] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(196,123,230,0.3),0_4px_18px_0_rgba(196,123,230,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(196,123,230,0.3),0_4px_18px_0_rgba(196,123,230,0.2)] focus:outline-none focus:ring-0 active:bg-electric-purple active:shadow-[0_8px_9px_-4px_rgba(196,123,230,0.3),0_4px_18px_0_rgba(196,123,230,0.2)]',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCouponDTO::class,
        ]);
    }
}
