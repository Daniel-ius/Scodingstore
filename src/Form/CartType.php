<?php

namespace App\Form;

use App\Entity\Cart;
use App\Form\EventListener\CheckoutListener;
use App\Form\EventListener\ClearCartListener;
use App\Form\EventListener\RemoveCartItemListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager =$manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => CartItemType::class
            ])
            ->add('save', SubmitType::class)
            ->add('clear', SubmitType::class)
            ->add('checkout', SubmitType::class)
            ->addEventSubscriber(new RemoveCartItemListener($this->manager))
            ->addEventSubscriber(new ClearCartListener($this->manager))
            ->addEventSubscriber(new CheckoutListener($this->manager));
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
