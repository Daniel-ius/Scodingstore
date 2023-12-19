<?php
namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'float', precision: 2)]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $category = null;

    #[ORM\OneToMany(mappedBy: 'orderRef', targetEntity: CartItem::class)]
    private $orders;

    public static array $categories = [
        'electronics',
        'clothing',
        'groceries',
    ];


    public function getId(): ?int
    {
        return $this->id;

    }//end getId()

    public function getName(): ?string
    {
        return $this->name;

    }//end getName()


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;

    }//end setName()

    public function getPrice(): ?float
    {
        return $this->price;

    }//end getPrice()

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;

    }//end setPrice()

    public function getDescription(): ?string
    {
        return $this->description;

    }//end getDescription()


    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;

    }//end setDescription()

    public function getCategory(): ?string
    {
        return $this->category;

    }//end getCategory()

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;

    }//end setCategory()

    public function getOrders(): ?CartItem
    {
        return $this->orders;

    }//end getOrders()

    public function setOrders(CartItem $orders): self
    {
        if ($orders->getId() !== $this) {
            $orders->setProduct($this);
        }
        $this->orders = $orders;
        return $this;

    }//end setOrders()
}//end class
