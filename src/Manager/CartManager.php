<?php
declare(strict_types=1);
namespace App\Manager;

use App\Entity\Cart;
use App\Factory\OrderFactory;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{

    private CartSessionStorage $cartSessionStorage;

    private OrderFactory $orderFactory;

    private EntityManagerInterface $entityManager;

    public function __construct(CartSessionStorage $cartSessionStorage, OrderFactory $orderFactory, EntityManagerInterface $entityManager)
    {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->orderFactory       = $orderFactory;
        $this->entityManager      = $entityManager;

    }//end __construct()

    public function getCurrentCart(): Cart
    {
        $cart = $this->cartSessionStorage->getCart();

        if (!$cart) {
            $cart = $this->orderFactory->create();
        }

        return $cart;

    }//end getCurrentCart()


    public function save(Cart $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        $this->cartSessionStorage->setCart($cart);

    }//end save()

    public function removeItem($item): void
    {
        $cart = $this->cartSessionStorage->getCart();
        $cart->removeItem($item);
        $this->entityManager->remove($item);
        $this->entityManager->flush();
        $this->cartSessionStorage->setCart($cart);

    }//end removeItem()

    public function getManager():CartManager
    {
        return $this;
    }
}//end class
