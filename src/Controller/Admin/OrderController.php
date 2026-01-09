<?php
namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\OrderStatusType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/order')]
class OrderController extends AdminController
{
    #[Route('/', name: 'admin_order_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        $status = $request->query->get('status');

        if ($status && in_array($status, array_keys(Order::getAvailableStatuses()))) {
            $orders = $orderRepository->findByStatus($status);
        } else {
            $orders = $orderRepository->findAllOrdered();
        }

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
            'currentStatus' => $status,
            'statuses' => Order::getAvailableStatuses(),
        ]);
    }

    #[Route('/{id}', name: 'admin_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $originalStatus = $order->getStatus();

        $form = $this->createForm(OrderStatusType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Symfony automatically binds form data to the entity via handleRequest()
            // The status is already updated in the $order object

            $entityManager->flush();

            $this->addFlash('success', sprintf('Order status updated from "%s" to "%s" successfully!', $originalStatus, $order->getStatus()));
            return $this->redirectToRoute('admin_order_show', ['id' => $order->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'There was an error updating the order status. Please try again.');
        }

        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }
}

