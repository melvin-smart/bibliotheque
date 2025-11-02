<?php

namespace App\Service;

use App\Dto\NewEmpruntDto;
use App\Entity\Emprunt;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class NewEmpunteurService
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security) {}

    public function 
    addEmprunt(NewEmpruntDto $newEmpruntDto)
    {
        //Initialiser la transaction
        $this->entityManager->beginTransaction();

        try {
            //Emprunteur
            $emprunteur = new User();
            $emprunteur->setNom($newEmpruntDto->nom);
            $emprunteur->setPrenom($newEmpruntDto->prenom);
            $emprunteur->setEmail($newEmpruntDto->email);
            $emprunteur->setPhone($newEmpruntDto->phone);
            $emprunteur->setAdresse($newEmpruntDto->adresse);
            $emprunteur->setDateInscription($newEmpruntDto->date_inscription);
            $this->entityManager->persist($emprunteur);

            //Emprunt
            $emprunt = new Emprunt();
            $emprunt->setLivre($newEmpruntDto->livre);
            $emprunt->setDateEmprunt($newEmpruntDto->date_emprunt);
            $emprunt->setDateRetour($newEmpruntDto->date_retour);
            $emprunt->setEmprunteur($emprunteur);
            $emprunt->setBibliothecaire($this->security->getUser());
            $emprunt->setDateValidation(new DateTimeImmutable());
            $emprunt->setStatut('Approuvé');
            $this->entityManager->persist($emprunt);

            //Ajustement de la qte
            $newEmpruntDto->livre->setQteDispo($newEmpruntDto->livre->getQteDispo() - 1);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable $th) {
            $this->entityManager->rollback();
            throw $th;
        }
    }
}
