<?php

namespace App\Form;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntDtoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'emprunteur',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom de l\'emprunteur',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('phone', TelType::class, [
                'label' => 'Telephone',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('date_inscription', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'inscription',
                'attr' => ['class' => 'form-control mb-3'],
                'html5' => true,
                'input' => 'datetime_immutable'
            ])
            ->add('livre', EntityType::class, [
                'class' => Livre::class,
                'label' => 'Livre',
                'choice_label' => 'titre',
                'attr' => ['class' => 'form-select mb-3'],
                'placeholder' => 'Selectionnez un livre',
                'query_builder' => function (LivreRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.qte_dispo > 0')
                        ->orderBy('l.id', 'DESC');
                }
            ])
            ->add('date_emprunt', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date emprunt',
                'attr' => ['class' => 'form-control mb-3'],
                'input' => 'datetime_immutable'
            ])
            ->add('date_retour', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date retour',
                'attr' => ['class' => 'form-control mb-3'],
                'input' => 'datetime_immutable'
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Approuvé' => 'Approuvé',
                    'En attente' => 'En attente',
                    'En retard' => 'En retard',
                    'Refusé' => 'Refusé',
                    'Rendu' => 'Rendu',
                ],
                'label' => 'Statut',
                'attr' => ['class' => 'form-select mb-3'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer', 
                'attr' => ['class' => 'btn btn-primary fw-bold shadow w-25 mb-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
