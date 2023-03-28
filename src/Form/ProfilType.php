<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\DTO\ProfilDTO;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('address')
            ->add('username')
            ->add('currentPassword', PasswordType::class)
            ->add('newPassword', PasswordType::class, [
                'required' => false
            ])
            ->add('confirmNewPassword', PasswordType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'inline-block rounded-full bg-danger px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#AE4CDC] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)] focus:outline-none focus:ring-0 active:bg-danger-700 active:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)]'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilDTO::class,
        ]);
    }
}
