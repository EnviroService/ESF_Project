<?php


namespace App\Form\EventListener;

use App\Repository\RateCardRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddModelFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event, RateCardRepository $rateCard)
    {
        $brand = $event->getData();
        $form = $event->getForm();
        $models = $rateCard->findAllModelsDistinct($brand);

        $form->add('models', ChoiceType::class, [
            'choices' => $models,
        ]);
    }
}