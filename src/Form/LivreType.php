<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur du livre',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'label' => 'Catégorie',
                'choice_label' => 'intitule',
                'attr' => ['class' => 'form-select mb-3']
            ])
            ->add('date_publication', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('qte_totale', IntegerType::class, [
                'label' => 'Quantité totale',
                'attr' => ['class' => 'form-control mb-3', 'min' => 0]
            ])
            ->add('qte_dispo', IntegerType::class, [
                'label' => 'Quantité disponible',
                'attr' => ['class' => 'form-control mb-3', 'min' => 0]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control mb-3'],
                'required' => false
            ])
            ->add('imgFiles', FileType::class, [
                'label' => 'Image(s) du livre',
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '2048k',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/jpg',
                                    'image/png'
                                ],
                                'mimeTypesMessage' => 'Veuillez uploader des images au format JPEG ou PNG valides'
                            ])
                        ]
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary w-25 fw-bold shadow mb-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
