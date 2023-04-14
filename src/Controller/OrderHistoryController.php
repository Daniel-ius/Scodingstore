<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\OrderHistoryRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderHistoryController extends AbstractController
{
    #[Route('/order/history', name: 'app_order_history')]
    public function index(OrderHistoryRepository $orderHistoryRepository): Response
    {
        $user =$this->getUser();
        $history = $orderHistoryRepository->findAllByID($user->getId());

        return $this->render('order_history/index.html.twig', [
            'histories' => $history,
        ]);
    }
}
