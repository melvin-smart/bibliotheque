<?php

namespace App\Controller\admin;

use App\Entity\ImgLivre;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('smart-biblio/admin/livre')]
class LivreController extends AbstractController
{

    /**
     * Create
     */
    #[Route('/new', name: 'admin_livre_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgFiles = $form->get('imgFiles')->getData();
            foreach ($imgFiles as $imgFile) {
                if ($imgFile) {
                    $originalName = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = $slugger->slug($originalName);
                    $newName = $safeName . '-' . uniqid() . $imgFile->guessExtension();

                    try {
                        $imgFile->move($this->getParameter('livre_directory'), $newName);
                    } catch (FileException $th) {
                        $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image ' . $th->getMessage());
                    }
                }

                $imgLivre = new ImgLivre();
                $imgLivre->setFilename($newName);
                $imgLivre->setLivre($livre);
                $em->persist($imgLivre);
                $livre->addImgLivre($imgLivre);
            }
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'Livre enregistré avec succès !');
            return $this->redirectToRoute('admin_livre_index');
        }
        return $this->render('admin/livre/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * read
     */
    #[Route('/', name: 'admin_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livre_repository): Response
    {
        $livres = $livre_repository->findAll();
        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    /**
     * update
     */
    #[Route('{id}/edit', name: 'admin_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Livre $livre): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newImgFiles = $form->get('imgFiles')->getData();
            if ($newImgFiles) {
                $oldImgFiles = $em->getRepository(ImgLivre::class)->findBy(['livre' => $livre]);
                foreach ($oldImgFiles as $oldImgFile) {
                    if ($oldImgFile) {
                        $oldImgPath = $this->getParameter('livre_directory').'/'. $oldImgFile->getFilename();
                        unlink($oldImgPath);
                        $livre->removeImgLivre($oldImgFile);
                    }
                }
            }

            foreach ($newImgFiles as $newImgFile) {
                if ($newImgFile) {
                    $originalName = pathinfo($newImgFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = $slugger->slug($originalName);
                    $newName = $safeName . '-' . uniqid() . '.' . $newImgFile->guessExtension();

                    try {
                        $newImgFile->move($this->getParameter('livre_directory'), $newName);
                    } catch (FileException $ex) {
                        $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image ' . $ex->getMessage());
                    }
                }
                $imgFile = new ImgLivre();
                $imgFile->setFilename($newName);
                $imgFile->setLivre($livre);
                $em->persist($imgFile);
                $livre->addImgLivre($imgFile);
            }
            $em->flush();
            $this->addFlash('success', 'Modification effectuée avec succès !');
            return $this->redirectToRoute('admin_livre_index');
        }
        return $this->render('admin/livre/edit.html.twig', [
            'form' => $form,
            'livre' => $livre,
        ]);
    }

    /**
     * Delete
     */
    #[Route('/{id}', name: 'admin_livre_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, Livre $livre): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            $em->remove($livre);
            $em->flush();

            $this->addFlash('success', 'Suppression effectuée avec succès !');
        } else {
            $this->addFlash('danger', 'Erreur de securité, Suppression impossible !');
        }

        return $this->redirectToRoute('admin_livre_index');
    }
}
