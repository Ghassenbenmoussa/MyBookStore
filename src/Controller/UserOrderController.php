<?php
namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/my')]
#[IsGranted('ROLE_ABONNE')]
class UserOrderController extends AbstractController
{
    #[Route('/orders', name: 'user_orders')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findByUser($this->getUser());

        return $this->render('user/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'user_order_show')]
    public function show(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot access this order.');
        }

        return $this->render('user/order_show.html.twig', [
            'order' => $order,
        ]);
    }
}

