<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_ABONNE')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Book $book, CartService $cartService, Request $request): Response
    {
        if ($this->isCsrfTokenValid('cart_add'.$book->getId(), $request->request->get('_token'))) {
            if ($book->getStock() > 0) {
                $cartService->add($book);
                $this->addFlash('success', 'Book added to cart!');
            } else {
                $this->addFlash('error', 'This book is out of stock.');
            }
        }

        return $this->redirectToRoute('catalog_book_show', ['id' => $book->getId()]);
    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(int $id, CartService $cartService, Request $request): Response
    {
        if ($this->isCsrfTokenValid('cart_remove'.$id, $request->request->get('_token'))) {
            $cartService->remove($id);
            $this->addFlash('success', 'Item removed from cart.');
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/clear', name: 'cart_clear', methods: ['POST'])]
    public function clear(CartService $cartService, Request $request): Response
    {
        if ($this->isCsrfTokenValid('cart_clear', $request->request->get('_token'))) {
            $cartService->clear();
            $this->addFlash('success', 'Cart cleared.');
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/checkout', name: 'cart_checkout', methods: ['GET', 'POST'])]
    public function checkout(CartService $cartService, EntityManagerInterface $entityManager, Request $request): Response
    {
        $cart = $cartService->getCart();

        if (empty($cart)) {
            $this->addFlash('warning', 'Your cart is empty.');
            return $this->redirectToRoute('catalog_books');
        }

        if ($request->isMethod('POST') && $this->isCsrfTokenValid('checkout', $request->request->get('_token'))) {
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setOrderDate(new \DateTime());
            $order->setStatus(Order::STATUS_PENDING);

            foreach ($cart as $item) {
                // Get fresh book entity from database to avoid detached entity error
                $bookId = $item['book']->getId();
                $book = $entityManager->getRepository(Book::class)->find($bookId);

                if (!$book) {
                    $this->addFlash('error', 'Book not found.');
                    return $this->redirectToRoute('cart_index');
                }

                $quantity = $item['quantity'];

                if ($book->getStock() < $quantity) {
                    $this->addFlash('error', sprintf('Not enough stock for "%s". Only %d available.', $book->getTitle(), $book->getStock()));
                    return $this->redirectToRoute('cart_index');
                }

                $orderItem = new OrderItem();
                $orderItem->setBook($book);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice($book->getPrice());
                $order->addOrderItem($orderItem);

                // Update stock
                $book->setStock($book->getStock() - $quantity);
            }

            $order->calculateTotal();
            $entityManager->persist($order);
            $entityManager->flush();

            $cartService->clear();

            $this->addFlash('success', sprintf('Order #%d placed successfully! Total: $%s', $order->getId(), $order->getTotalAmount()));
            return $this->redirectToRoute('user_order_show', ['id' => $order->getId()]);
        }

        return $this->render('cart/checkout.html.twig', [
            'cart' => $cart,
            'total' => $cartService->getTotal(),
        ]);
    }
}

