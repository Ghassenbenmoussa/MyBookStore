<?php
namespace App\Controller\Admin;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin/author')]
class AuthorController extends AdminController
{
    #[Route('/', name: 'admin_author_index', methods: ['GET'])]
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('admin/author/index.html.twig', [
            'authors' => $authorRepository->findAllOrdered(),
        ]);
    }
    #[Route('/new', name: 'admin_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();
            $this->addFlash('success', 'Author created successfully!');
            return $this->redirectToRoute('admin_author_index');
        }
        return $this->render('admin/author/new.html.twig', ['author' => $author, 'form' => $form]);
    }
    #[Route('/{id}', name: 'admin_author_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', ['author' => $author]);
    }
    #[Route('/{id}/edit', name: 'admin_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Author updated successfully!');
            return $this->redirectToRoute('admin_author_index');
        }
        return $this->render('admin/author/edit.html.twig', ['author' => $author, 'form' => $form]);
    }
    #[Route('/{id}', name: 'admin_author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $entityManager->remove($author);
            $entityManager->flush();
            $this->addFlash('success', 'Author deleted successfully!');
        }
        return $this->redirectToRoute('admin_author_index');
    }
}
