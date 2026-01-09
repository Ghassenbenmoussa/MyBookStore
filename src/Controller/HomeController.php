<?php
namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $bookRepository, CategoryRepository $categoryRepository): Response
    {
        $featuredBooks = $bookRepository->createQueryBuilder('b')
            ->where('b.stock > 0')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        $categories = $categoryRepository->findAllOrdered();

        return $this->render('home/index.html.twig', [
            'featuredBooks' => $featuredBooks,
            'categories' => $categories,
        ]);
    }
}
