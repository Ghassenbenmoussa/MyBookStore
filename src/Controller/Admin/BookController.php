<?php
namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/book')]
class BookController extends AdminController
{
    #[Route('/', name: 'admin_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('admin/book/index.html.twig', [
            'books' => $bookRepository->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverImageFile = $form->get('coverImageFile')->getData();

            if ($coverImageFile) {
                $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImageFile->guessExtension();

                try {
                    $coverImageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/books',
                        $newFilename
                    );
                    $book->setCoverImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload cover image');
                }
            }

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Book created successfully!');
            return $this->redirectToRoute('admin_book_index');
        }

        return $this->render('admin/book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverImageFile = $form->get('coverImageFile')->getData();

            if ($coverImageFile) {
                $originalFilename = pathinfo($coverImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverImageFile->guessExtension();

                try {
                    $coverImageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/books',
                        $newFilename
                    );

                    // Delete old image if exists
                    if ($book->getCoverImage()) {
                        $oldImagePath = $this->getParameter('kernel.project_dir').'/public/uploads/books/'.$book->getCoverImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $book->setCoverImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload cover image');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Book updated successfully!');
            return $this->redirectToRoute('admin_book_index');
        }

        return $this->render('admin/book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            // Delete cover image if exists
            if ($book->getCoverImage()) {
                $imagePath = $this->getParameter('kernel.project_dir').'/public/uploads/books/'.$book->getCoverImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($book);
            $entityManager->flush();
            $this->addFlash('success', 'Book deleted successfully!');
        }

        return $this->redirectToRoute('admin_book_index');
    }
}
