<?php

namespace App\Form;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\DTO\AddFriendDTO;


class AddFriendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users = $options['users'];
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => $users,
                'choice_label' => 'username',
                'label' => 'Select user',
                'attr' => [
                    'class' => 'form-control form-control w-full py-1 rounded-lg px-3 focus:outline-none focus:border-electric-purple hover:shadow bg-dark-purple my-4'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add friend',
                'attr' => [
                    'class' => 'btn btn-primary inline-block rounded-full bg-danger px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#AE4CDC] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)] focus:outline-none focus:ring-0 active:bg-danger-700 active:shadow-[0_8px_9px_-4px_rgba(174,76,220,0.3),0_4px_18px_0_rgba(174,76,220,0.2)]'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => AddFriendDTO::class,
            'users' => null,
        ]);
    }
}
