<?php
declare(strict_types=1);

namespace App\Form\EventListener;

use App\Entity\Cart;
use App\Entity\OrderHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CheckoutListener implements EventSubscriberInterface
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }//end __construct()


    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }//end getSubscribedEvents()

    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $cart = $form->getData();
        if (!$cart instanceof Cart) {
            return;
        }
        if (!$form->get('checkout')->isClicked()) {
            return;
        }
        $items= $cart->getItems();
        foreach ($items as $item) {
            $item->setTotalPrice($item->getQuantity() * $item->getProduct()->getPrice());
            $this->manager->persist($item);
        }
        $history = new OrderHistory();
        $history->addCart($cart);
        $history->setUser($cart->getUser());
        $cart->setOrderHistory($history);
        $cart->setStatus(Cart::STATUS_CHECKOUT);
        $this->manager->persist($history);
        $this->manager->flush();
    }

}//end class
