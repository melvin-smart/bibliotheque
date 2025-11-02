<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Entity\User;
use App\Repository\LivreRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_emprunt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'emprunt',
                'attr' => ['class' => 'form-control mb-3'],
                'html5' => true,
                'input' => 'datetime_immutable',
            ])
            ->add('date_retour', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de retour',
                'attr' => ['class' => 'form-control mb-3'],
                'html5' => true,
                'input' => 'datetime_immutable'
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'attr' => ['class' => 'form-select mb-3'],
                'choices' => [
                    'En attente' => 'En attente',
                    'Approuvé' => 'Approuvé',
                    'Refusé' => 'Refusé',
                    'Rendu' => 'Rendu',
                    'En retard' => 'En retard',
                ]
            ])
            ->add('emprunteur', EntityType::class, [
                'class' => User::class,
                'label' => 'Emprunteur',
                'choice_label' => function(User $user) {
                    return $user->getNom(). ' '. $user->getPrenom() ;
                },
                'attr' => ['class' => 'form-select mb-3'],
                'placeholder' => 'Selectionnez un emprunteur',
                'query_builder' => function(UserRepository $er) {
                    return $er->createQueryBuilder('u')
                            ->where('u.roles LIKE :role')
                            ->setParameter('role', '%"ROLE_EMPRUNTEUR"%')
                            ->orderBy('u.id', 'DESC');
                }

            ])
            ->add('livre', EntityType::class, [
                'class' => Livre::class,
                'choice_label' => 'titre',
                'attr' => ['class' => 'form-select mb-3'],
                'placeholder' => 'Selectionnez un livre',
                'label' => 'Livre',
                'query_builder' => function(LivreRepository $er) {
                    return $er->createQueryBuilder('l')
                            ->where('l.qte_dispo > 0')
                            ->orderBy('l.id', 'DESC');
                }
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer', 
                'attr' => ['class' => 'btn btn-primary mb-3 w-25 fw-bold shadow']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
            'validation_mode' => false,
        ]);
    }
}
