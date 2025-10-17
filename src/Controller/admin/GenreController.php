<?php
namespace App\Controller\admin;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('smart-biblio/admin/genre')]
class GenreController extends AbstractController {

    /**
     * Create
     */
    #[Route('/new', name:'admin_genre_new', methods:['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($genre);
            $em->flush();
            $this->addFlash('success', 'Genre enregistré avec succes !');
            return $this->redirectToRoute('admin_genre_index');
        }

        return $this->render('admin/genre/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * read
     */
    #[Route('/', name:'admin_genre_index', methods:['GET'])]
    public function index(GenreRepository $genre_repository): Response {
        $genres = $genre_repository->findAll();

        return $this->render('admin/genre/index.html.twig', [
            'genres' => $genres,
        ]);
    }

    /**
     * update
     */
    #[Route('{id}/edit', name:'admin_genre_edit', methods:['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, Genre $genre): Response {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Modification enregistrée avec succès !');
            return $this->redirectToRoute('admin_genre_index');
        }

        return $this->render('admin/genre/edit.html.twig', [
            'form' => $form->createView(),
            'genre' => $genre,
        ]);
    }

    /**
     * Delete
     */
    #[Route('/{id}', name:'admin_genre_delete', methods:['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, Genre $genre): Response {
        if ($this->isCsrfTokenValid('delete'.$genre->getId(), $request->request->get('_token'))) {
            $em->remove($genre);
            $em->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès !');
        }
        else {
            $this->addFlash('danger', 'Erreur de sécurité, Suppression impossible !');
        }

        return $this->redirectToRoute('admin_genre_index');
    }
}
?>