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
                 'label' => 'Save',
                 'attr' => [
                     'class' => 'btn inline-block rounded-full bg-electric-purple px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#C47BE6] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(196,123,230,0.3),0_4px_18px_0_rgba(196,123,230,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(196,123,230,0.3),0_4px_18px_0_rgba(196,123,230,0.2)] focus:outline-none focus:ring-0 active:bg-electric-purple active:shadow-[0_8px_9px_-4px_rgba(196,123,230,0.3),0_4px_18px_0_rgba(196,123,230,0.2)]',
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
