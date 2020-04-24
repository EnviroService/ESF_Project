<?php

namespace App\Form;

use App\Entity\Tracking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imei')
            ->add('isSent')
            ->add('sentDate')
            ->add('isReceived')
            ->add('receivedDate')
            ->add('isRepaired')
            ->add('repairedDate')
            ->add('isReturned')
            ->add('returnedDate')
            ->add('booking')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tracking::class,
        ]);
    }
}
