<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AddGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            // add category to the form and show their names
            ->add('category', EntityType::class, [
                'multiple' => true,
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('description')
            ->add('price')
            ->add('version')
            ->add('picture')
            ->add('file')
            // submit button
            ->add('submit', SubmitType::class, [
                'label' => 'Add game',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
