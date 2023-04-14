<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $quantity = 1;

    #[ORM\Column(type: 'float', precision: 2, scale: 2)]
    private float $totalPrice = 0.0;

    #[ORM\ManyToOne(targetEntity: Products::class)]
    private ?Products $product;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    private ?Cart $orderRef;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getOrderRef(): ?Cart
    {
        return $this->orderRef;
    }

    public function setOrderRef(?Cart $orderRef): self
    {
        $this->orderRef = $orderRef;

        return $this;
    }

    public function getTotal(): float
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }

    public function equals(CartItem $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }
}
