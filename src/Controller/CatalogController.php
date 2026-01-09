<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Editor;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\EditorRepository;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    #[Route('/books', name: 'catalog_books')]
    public function books(Request $request, BookRepository $bookRepository): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $books = $bookRepository->createQueryBuilder('b')
                ->where('b.title LIKE :search')
                ->andWhere('b.stock > 0')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('b.title', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            $books = $bookRepository->findInStock();
        }

        return $this->render('catalog/books.html.twig', [
            'books' => $books,
            'search' => $search,
        ]);
    }

    #[Route('/book/{id}', name: 'catalog_book_show')]
    public function bookShow(Book $book): Response
    {
        return $this->render('catalog/book_show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/categories', name: 'catalog_categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllOrdered();

        return $this->render('catalog/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'catalog_category_show')]
    public function categoryShow(Category $category, BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findByCategory($category);

        return $this->render('catalog/category_show.html.twig', [
            'category' => $category,
            'books' => $books,
        ]);
    }

    #[Route('/editors', name: 'catalog_editors')]
    public function editors(EditorRepository $editorRepository): Response
    {
        $editors = $editorRepository->findAllOrdered();

        return $this->render('catalog/editors.html.twig', [
            'editors' => $editors,
        ]);
    }

    #[Route('/editor/{id}', name: 'catalog_editor_show')]
    public function editorShow(Editor $editor, BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findByEditor($editor);

        return $this->render('catalog/editor_show.html.twig', [
            'editor' => $editor,
            'books' => $books,
        ]);
    }

    #[Route('/authors', name: 'catalog_authors')]
    public function authors(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAllOrdered();

        return $this->render('catalog/authors.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/{id}', name: 'catalog_author_show')]
    public function authorShow(Author $author): Response
    {
        return $this->render('catalog/author_show.html.twig', [
            'author' => $author,
        ]);
    }
}

