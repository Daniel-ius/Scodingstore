<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProductFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user   = new User();
        $user->setEmail('admin@admin.com');
        $user->setName('admin');
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                'admin'
            )
        );
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        for ($i = 0; $i < 1000; $i++) {
            $product = new Product();
            $product->setName("Product $i");
            $product->setPrice(round((mt_rand(5, 600) * 0.25 / 0.33), 2));
            $product->setCategory(Product::$categories[mt_rand(0, (count(Product::$categories) - 1))]);
            $product->setDescription("Product description $i");
            $manager->persist($product);
        }

        $manager->flush();

    }//end load()


}//end class
