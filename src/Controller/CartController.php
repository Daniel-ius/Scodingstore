<?php
declare(strict_types=1);
namespace App\Controller;

use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CartController extends AbstractController
{
    private UserInterface $user;
    #[Route('/cart', name: 'app_cart')]
    public function index(CartManager $cartManager, Request $request): Response
    {
        $this->user=$this->getUser();
        $cart = $cartManager->getCurrentCart();
        $cart->setUser($this->user);
        $form = $this->createForm(CartType::class, $cart);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);

            return $this->redirectToRoute('cart');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }
}
