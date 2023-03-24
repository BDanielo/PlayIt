<?php

namespace App\Form;

use App\Entity\Games;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // !TODO replace hard coded category with dynamic category from db
        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            //!TODO add category
            // ->add('category', ChoiceType::class, [
            //     'choices'  => [
            //         'Horror' => 'Horror',
            //         'FPS' => 'FPS',
            //         'PUZZLE' => 'PUZZLE',
            //     ],
            // ])
            ->add('version')
            ->add('picture', FileType::class, [
                'label' => 'Picture (jpg file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => true,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '10m',
                        'mimeTypes' => [
                            'application/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG document',
                    ])
                ],
            ])
            // submit button
            ->add('submit', SubmitType::class, [
                'label' => 'Add Game',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
