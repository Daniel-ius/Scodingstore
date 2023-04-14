<?php
declare(strict_types=1);
namespace App\Form\EventListener;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RemoveCartItemListener implements EventSubscriberInterface
{

    private EntityManagerInterface $manager;


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager =$manager;

    }//end __construct()


    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];

    }//end getSubscribedEvents()


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

        // Removes items from the cart
        foreach ($form->get('items')->all() as $child) {
            if ($child->get('remove')->isClicked()) {
                $item = $child->getData();
                $cart->removeItem($item);
                $this->manager->remove($item);
                $this->manager->flush();
                $this->manager->persist($cart);
                break;
            }
        }

    }//end postSubmit()


}//end class
