<?php
declare(strict_types=1);
namespace App\Factory;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Products;


class OrderFactory
{

    public function create(): Cart
    {
        $order = new Cart();
        $order
            ->setStatus(Cart::STATUS_CART)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $order;

    }//end create()


    /**
     * @param Products $product
     *
     * @return CartItem
     */
    public function createItem(Products $product): CartItem
    {
        $item = new CartItem();
        $item->setTotalPrice($product->getPrice());
        $item->setProduct($product);
        $item->setQuantity(1);
        $item->setOrderRef($this->create());

        return $item;

    }//end createItem()


}//end class
