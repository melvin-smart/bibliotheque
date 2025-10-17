<?php
namespace App\Controller\admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('smart-biblio/admin/emprunteur')]
class EmprunteurController extends AbstractController
{
    /**
     * create
     */
    #[Route('/new', name:'admin_emprunteur_new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emprunteur = new User();
        $form = $this->createForm(UserType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emprunteur->setRoles(['ROLE_EMPRUNTEUR']);
            $entityManager->persist($emprunteur);
            $entityManager->flush();

            $this->addFlash('success', 'Emprunteur enregistré avec succès');
            return $this->redirectToRoute('admin_emprunteur_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
            'user' => 'emprunteur',
        ]);
    }

    /**
     * Read
     */
    #[Route('/', name:'admin_emprunteur_index', methods:['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $emprunteurs = $userRepository->findByRole('ROLE_EMPRUNTEUR');

        return $this->render('admin/user/index.html.twig', [
            'emprunteurs' => $emprunteurs,
            'user' => 'emprunteur',
        ]);
    }

    /**
     * Update
     */
    #[Route('{id}/edit', name:'admin_emprunteur_edit', methods:['GET', 'POST'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, User $emprunteur): Response
    {
        $form = $this->createForm(UserType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Modification enregistrée avec succès');
            return $this->redirectToRoute('admin_emprunteur_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'emprunteur' => $emprunteur,
            'user' => 'emprunteur',
        ]);
    }
}
?>