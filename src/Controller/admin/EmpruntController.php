<?php

namespace App\Controller\admin;

use App\Dto\NewEmpruntDto;
use App\Entity\Emprunt;
use App\Form\EmpruntDtoType;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use App\Service\NewEmpunteurService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('smart-biblio/admin/emprunt')]
class EmpruntController extends AbstractController
{

    /**
     * Create
     */
    #[Route('/new', name: 'admin_emprunt_new', methods: ['POST', 'GET'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(new DateTimeImmutable());
        $emprunt->setDateRetour((new DateTimeImmutable())->modify('+14 days'));
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emprunt->setReference('SB-Emp-' . uniqid());
            $emprunt->setStatut('Approuvé');
            $emprunt->setBibliothecaire($this->getUser());
            $emprunt->setDateValidation(new DateTimeImmutable());

            $livre = $emprunt->getLivre();
            $livre->setQteDispo($livre->getQteDispo() - 1);

            $entityManager->persist($emprunt);
            $entityManager->flush();

            $this->addFlash('success', 'Emprunt enregistré avec succès !');
            return $this->redirectToRoute('admin_emprunt_index');
        }

        return  $this->render('admin/emprunt/new.html.twig', [
            'form' => $form->createView(),
            'emprunt' => $emprunt,
        ]);
    }

    /**
     * Read
     */
    #[Route('/', name: 'admin_emprunt_index', methods: ['GET'])]
    public function index(EmpruntRepository $empruntRepository): Response
    {
        $emprunts = $empruntRepository->findAll();

        return $this->render('admin/emprunt/index.html.twig', [
            'emprunts' => $emprunts,
        ]);
    }

    /**
     * Update
     */
    #[Route('{id}/edit', name: 'admin_emprunt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Emprunt $emprunt): Response
    {
        $oldLivre = $emprunt->getLivre();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newLivre = $emprunt->getLivre();

            if ($oldLivre != $newLivre) {
                $oldLivre->setQteDispo($oldLivre->getQteDispo() + 1);
                $newLivre->setQteDispo($newLivre->getQteDispo() - 1);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Modification enregistrée avec succès !');
            return $this->redirectToRoute('admin_emprunt_index');
        }
        return $this->render('admin/emprunt/edit.html.twig', [
            'form' => $form,
            'emprunt' => $emprunt,
        ]);
    }

    /**
     * delete
     */
    #[Route('/{id}', name: 'admin_emprunt_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Emprunt $emprunt): Response
    {
        if ($this->isCsrfTokenValid('delete' . $emprunt->getId(), $request->request->get('_token'))) {
            $entityManager->remove($emprunt);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression effectuée avec succès');
        } else {
            $this->addFlash('danger', 'Erreur de securite, suppression impossible');
        }

        return $this->redirectToRoute('admin_emprunt_index');
    }

    /**
     * Approuver
     */
    #[Route('{id}/validate', name: 'admin_emprunt_valider', methods: ['POST'])]
    public function validate(Emprunt $emprunt, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('validate' . $emprunt->getId(), $request->request->get('_token'))) {
            $emprunt->setStatut('Approuvé');
            $emprunt->setDateValidation(new DateTimeImmutable());
            $emprunt->setBibliothecaire($this->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'Emprunt approuvé avec succès !');
        } else {
            $this->addFlash('danger', 'Erreur de securité, impossible d\'approuver l\'emprunt !');
        }

        return $this->redirectToRoute('admin_emprunt_index');
    }

    /**
     * Refuser
     */
    #[Route('{id}/refuse', name: 'admin_emprunt_refuser', methods: ['POST'])]
    public function refuse(Request $request, EntityManagerInterface $entityManager, Emprunt $emprunt): Response
    {
        if ($this->isCsrfTokenValid('refuse' . $emprunt->getId(), $request->request->get('_token'))) {
            $emprunt->setStatut('Refusé');
            $emprunt->setDateValidation(new DateTimeImmutable());
            $emprunt->setBibliothecaire($this->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'L\'emprunt a été refusé !');
        } else {
            $this->addFlash('danger', 'Erreur de securité, refus impossible !');
        }

        return $this->redirectToRoute('admin_emprunt_index');
    }

    /**
     * Rendu
     */
    #[Route('{id}/rendu', name: 'admin_emprunt_rendu', methods: ['POST'])]
    public function rendu(Request $request, EntityManagerInterface $entityManager, Emprunt $emprunt): Response
    {
        if ($this->isCsrfTokenValid('rendu' . $emprunt->getId(), $request->request->get('_token'))) {
            $emprunt->setStatut('Rendu');
            $emprunt->setDateRetourReelle(new DateTimeImmutable());
            $emprunt->getLivre()->setQteDispo($emprunt->getLivre()->getQteDispo() + 1);

            $entityManager->flush();
            $this->addFlash('success', 'L\'emprunt a été marqué comme rendu !');
        } else {
            $this->addFlash('danger', 'Erreur de securité, impossible de marquer comme rendu');
        }

        return $this->redirectToRoute('admin_emprunt_index');
    }

    /**
     * Create and Link
     */
    #[Route('/link', name: 'admin_emprunt_create', methods: ['POST', 'GET'])]
    public function create(Request $request, NewEmpunteurService $newEmpunteurService): Response
    {
        $newEmprunt = new NewEmpruntDto();
        $newEmprunt->setDateEmprunt(new DateTimeImmutable());
        $newEmprunt->setDateInscription(new DateTimeImmutable());
        $newEmprunt->setDateRetour((new DateTimeImmutable())->modify('+ 14 days'));

        $form = $this->createForm(EmpruntDtoType::class, $newEmprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->addFlash('success', 'Emprunt enregistré avec succès !');
                return $this->redirectToRoute('admin_emprunt_indexx');
            } catch (Exception $ex) {
                $this->addFlash('danger', 'Une erreur est survenue ' . $ex->getMessage());
            }
        }

        return $this->render('admin/emprunt/create.html.twig', [
            'form' => $form->createView(),
            'newEmprunt' => $newEmprunt,
        ]);
    }
}
