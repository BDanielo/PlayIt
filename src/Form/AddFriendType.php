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
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add friend',
                'attr' => [
                    'class' => 'btn btn-primary'
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
