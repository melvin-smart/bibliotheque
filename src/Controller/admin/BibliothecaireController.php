<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('smart-biblio/admin/bibliothecaire')]
class BibliothecaireController extends AbstractController
{
    /**
     * Create
     */
    #[Route('/new', name: 'admin_bibliothecaire_new', methods: ['POST', 'GET'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $bibliothecaire = new User();
        $form = $this->createForm(UserType::class, $bibliothecaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pass = $form->get('password')->getData();
            $bibliothecaire->setRoles(['ROLE_BIBLIOTHECAIRE']);
            $bibliothecaire->setPassword($userPasswordHasherInterface->hashPassword($bibliothecaire, $pass));
            $entityManager->persist($bibliothecaire);
            $entityManager->flush();

            $this->addFlash('success', 'Bibliothecaire enregistré avec succès');
            return $this->redirectToRoute('admin_bibliothecaire_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
            'user' => 'bibliothecaire',
        ]);
    }

    /**
     * Read
     */
    #[Route('/', name: 'admin_bibliothecaire_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $bibliothecaires = $userRepository->findByRole("ROLE_BIBLIOTHECAIRE");

        return $this->render('admin/user/index.html.twig', [
            'bibliothecaires' => $bibliothecaires,
            'user' => 'bibliothecaire',
        ]);
    }

    /**
     * Update
     */
    #[Route('{id}/edit', name: 'admin_bibliothecaire_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $bibliothecaire, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $form = $this->createForm(UserType::class, $bibliothecaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pass = $form->get('password')->getData();
            if ($pass) {
                $bibliothecaire->setPassword($userPasswordHasherInterface->hashPassword($bibliothecaire, $pass));
                $entityManager->flush();
            } else {
                $entityManager->flush();
            }
            $this->addFlash('success', 'Modification enregistrée avec succès');
            return $this->redirectToRoute('admin_bibliothecaire_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'bibliothecaire' => $bibliothecaire,
            'user' => 'bibliothecaire',
        ]);
    }
}
