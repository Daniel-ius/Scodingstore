<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\CartRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\Table(name: '`cart`')]
class Cart
{
    public function __toString(): string
    {
        return (string) $this->id;
    }

    const STATUS_CART = 'cart';
    const STATUS_CHECKOUT = 'checkout';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'orderRef', targetEntity: CartItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status = self::STATUS_CART;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt;
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $userID;

    #[ORM\ManyToOne(inversedBy: 'cart')]
    private ?OrderHistory $orderHistory = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getUser(): User
    {
        return $this->userID;
    }

    public function setUser(User $user): self
    {
        $this->userID = $user;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
    public function addItem(CartItem $item): self
    {
        foreach ($this->getItems() as $existingItem) {
            // The item already exists, update the quantity
            if ($existingItem->equals($item)) {
                $existingItem->setQuantity(
                    $existingItem->getQuantity() + $item->getQuantity()
                );
                return $this;
            }
        }
        $this->items[] = $item;
        $item->setOrderRef($this);

        return $this;
    }

    public function removeItem(CartItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderRef() === $this) {
                $item->setOrderRef(null);
            }
        }
        return $this;
    }

    public function removeItems(): self
    {
        foreach ($this->getItems() as $item) {
            $this->removeItem($item);
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    public function getOrderHistory(): ?OrderHistory
    {
        return $this->orderHistory;
    }

    public function setOrderHistory(?OrderHistory $orderHistory): self
    {
        $this->orderHistory = $orderHistory;

        return $this;
    }
}
