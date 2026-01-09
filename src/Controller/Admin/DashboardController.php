<?php
namespace App\Controller\Admin;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin')]
class DashboardController extends AdminController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        BookRepository $bookRepository,
        CategoryRepository $categoryRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository
    ): Response {
        $totalBooks = count($bookRepository->findAll());
        $totalCategories = count($categoryRepository->findAll());
        $totalOrders = count($orderRepository->findAll());
        $totalUsers = count($userRepository->findAll());
        $recentOrders = $orderRepository->findBy([], ['orderDate' => 'DESC'], 5);
        $lowStockBooks = $bookRepository->createQueryBuilder('b')
            ->where('b.stock <= 10')
            ->andWhere('b.stock > 0')
            ->orderBy('b.stock', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        $outOfStockBooks = $bookRepository->createQueryBuilder('b')
            ->where('b.stock = 0')
            ->orderBy('b.title', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        return $this->render('admin/dashboard/index.html.twig', [
            'totalBooks' => $totalBooks,
            'totalCategories' => $totalCategories,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders,
            'lowStockBooks' => $lowStockBooks,
            'outOfStockBooks' => $outOfStockBooks,
        ]);
    }
}
