<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;


class ProductsController extends AbstractController
{

    private RateLimiterFactory $factory;

    private InMemoryStorage $storage;

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {

        $this->cache = $cache;
        $this->factory = new RateLimiterFactory(
            [
                'id' => 'default',
                'policy' => 'token_bucket',
                'limit' => 2,
                'rate' => ['interval' => '15 minutes'],
            ],
            $this->storage = new InMemoryStorage()
        );

    }//end __construct()

    /**
     * @throws InvalidArgumentException
     */
    #[Route(path: '/', name: 'list_product', methods: ['GET'])]
    public function list(ProductsRepository $repository, Request $request): Response
    {
        $searchTerm = $request->query->get('search', '');
        $category = $request->query->get('category', '');
        $limiter = $this->factory->create($request->getClientIp());
        $limit = $limiter->consume();
        $headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp(),
            'X-RateLimit-Limit' => $limit->getLimit(),
        ];
        if ($limit->isAccepted() === false) {
            return new Response(null, Response::HTTP_TOO_MANY_REQUESTS, $headers);
        }
        $products = $this->cache->get(sprintf('products_%s_%s', $category, $searchTerm), function () use ($repository, $category, $searchTerm) {
            return $repository->findBySearchAndCategory($searchTerm, $category);
        });
        return $this->render('product/index.html.twig',
            [
                'products' => $products,
                'search' => $searchTerm,
                'category' => $category,
                'categories' => Product::$categories,
            ]);
    }//end list()

    #[Route('/product/{id}', name: 'detail_product', methods: ['GET', 'POST'])]
    public function detail(Request $request, CartManager $cartManager,ProductsRepository $repository): Response
    {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        $products = $repository->find($request->get('id'));

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($products);

            $cart = $cartManager->getCurrentCart();
            $cart->addItem($item)->setUpdatedAt(new \DateTime());

            $cartManager->save($cart);

            return $this->redirectToRoute('app_cart', ['id' => $products->getId()]);
        }

        return $this->render(
            'product/detail.html.twig',
            [
                'product' => $products,
                'form' => $form->createView(),
            ]
        );

    }//end detail()


}//end class
