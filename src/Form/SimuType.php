<?php


namespace App\Form;

use App\Entity\RateCard;
use App\Form\EventListener\AddModelFieldSubscriber;
use App\Repository\RateCardRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimuType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    )
    {
        $builder->add('brand', EntityType::class, [
            'class' => RateCard::class,
            'choice_label' => 'brand'
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $brand = $form->get('brand')->getData();
            $models = $options['rateCards']->findAllModelsDistinct($brand);

            $form->add('models', ChoiceType::class, [
                'choices' => $models,
            ]);
        });

            $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RateCard::class,
            'rateCards' => RateCardRepository::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO: Change the autogenerated stub
//        return parent::getBlockPrefix(); // TODO: Change the autogenerated stub
    }
}