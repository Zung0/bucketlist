<?php

namespace App\Form;

use App\Entity\Wish;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false

            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Sport' => 'Sport',
                    'Travel & Adventure' => 'Travel & Adventure',
                    'Entertainement' => 'Entertainement',
                    'Human relations' => 'Human relations',
                    'Other' => 'Other',
                ]
            ])
            ->add('author')
            ->add('isPublished')
            ->add('image_file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Ton image est trop lourde',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Ce Format n'est pas pris en charge"
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'register',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
                $wish = $formEvent->getData();
                if ($wish && $wish->getImage()) {
                    $form = $formEvent->getForm();
                    $form->add('delete_image', CheckboxType::class, [
                        'mapped' => false,
                        'required' => false
                    ]);
                }
            });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}
