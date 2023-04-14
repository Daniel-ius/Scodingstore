<?php

namespace App\Form\EventListener;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ClearCartListener implements EventSubscriberInterface
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    /**
     * @param FormEvent $event
     *
     * @return void
     */
    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $cart = $form->getData();

        if (!$cart instanceof Cart) {
            return;
        }

        if (!$form->get('clear')->isClicked()) {
            return;
        }
        $items = $cart->getItems();
        foreach ($items as $item) {
            $this->manager->remove($item);
            $this->manager->flush();
        }
        $this->manager->persist($cart);
    }
}