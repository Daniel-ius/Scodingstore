<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __toString(): string
    {
        return (string) $this->id;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OrderHistory::class, orphanRemoval: true)]
    private Collection $orderHistories;

    public function __construct()
    {
        $this->orderHistories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    public function getEmail(): ?string
    {
        return $this->email;

    }//end getEmail()


    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;

    }//end setEmail()


    public function getPassword(): ?string
    {
        return $this->password;

    }//end getPassword()


    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;

    }//end setPassword()


    public function getName(): ?string
    {
        return $this->name;

    }//end getName()


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;

    }//end setName()


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;

    }//end setRoles()


    public function eraseCredentials(): void
    {

    }//end eraseCredentials()


    public function getUserIdentifier(): string
    {
        return $this->name;

    }//end getUserIdentifier()


    public function getRoles(): array
    {
        return $this->roles;

    }//end getRoles()

    /**
     * @return Collection<int, OrderHistory>
     */
    public function getOrderHistories(): Collection
    {
        return $this->orderHistories;
    }

    public function addOrderHistory(OrderHistory $orderHistory): self
    {
        if (!$this->orderHistories->contains($orderHistory)) {
            $this->orderHistories->add($orderHistory);
            $orderHistory->setUser($this);
        }

        return $this;
    }

    public function removeOrderHistory(OrderHistory $orderHistory): self
    {
        if ($this->orderHistories->removeElement($orderHistory)) {
            // set the owning side to null (unless already changed)
            if ($orderHistory->getUser() === $this) {
                $orderHistory->setUser(null);
            }
        }

        return $this;
    }


}//end class
