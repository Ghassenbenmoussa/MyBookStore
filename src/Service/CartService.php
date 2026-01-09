<?php
namespace App\Service;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    public function add(Book $book): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        $id = $book->getId();

        if (!empty($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'book' => $book,
                'quantity' => 1
            ];
        }

        $session->set('cart', $cart);
    }

    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
    }

    public function updateQuantity(int $id, int $quantity): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]['quantity'] = max(1, $quantity);
        }

        $session->set('cart', $cart);
    }

    public function clear(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('cart');
    }

    public function getTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['book']->getPrice() * $item['quantity'];
        }

        return $total;
    }

    public function getItemCount(): int
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }
}

