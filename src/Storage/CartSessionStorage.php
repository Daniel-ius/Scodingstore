<?php

namespace App\Storage;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartSessionStorage
{
    private RequestStack $requestStack;
    private CartRepository $orderRepository;

    const CART_KEY_NAME = 'cart_id';

    /**
     * @param RequestStack $requestStack
     * @param CartRepository $orderRepository
     */
    public function __construct(RequestStack $requestStack, CartRepository $orderRepository)
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return int|null
     */
    private function getCartId(): ?int
    {
        return $this->requestStack->getSession()->get(self::CART_KEY_NAME);
    }

    /**
     * @param Cart $cart
     * @return void
     */
    public function setCart(Cart $cart): void
    {
        $this->requestStack->getSession()->set(self::CART_KEY_NAME, $cart->getId());
    }

    /**
     * @return Cart|null
     */
    public function getCart(): ?Cart
    {
        return $this->orderRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Cart::STATUS_CART,
        ]);
    }
}
