<?php

namespace App\Form;

use App\Entity\Tracking;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Imei')
            ->add('brand', TextType::class, ['mapped' => false,'label' => 'Marque'])
            ->add('model', TextType::class, ['mapped' => false,'label' => 'Model'])
            ->add('solution', TextType::class, ['mapped' => false,'label' => 'Réparations'])
            ->add('isReceived',ChoiceType::class,[
                'label' => 'Reçu :',
                    'expanded' => true,
                    'choices' => [
                        'oui' => '1',
                        'non' => '0'
                    ]
            ])
            ->add('isRepaired',ChoiceType::class,[
                'label' => 'Part-il en réparation :',
                'expanded' => true,
                'choices' => [
                    'oui' => '1',
                    'non' => '0'
                ]
            ])
            ->add('isReturned',ChoiceType::class,[
                'label' => 'Est-il renvoyé au client :',
                'expanded' => true,
                'choices' => [
                    'oui' => '1',
                    'non' => '0'
                ]
            ]) ->add('receivedDate', DateTimeType::class, [
                'label' => 'Date et heure réception',
                'date_widget' => 'single_text',
            ])
            ->add('repairedDate', DateTimeType::class, [
                'label' => 'Date et heure de réparation',
                'date_widget' => 'single_text',
            ])
            ->add('returnedDate', DateTimeType::class, [
                'label' => 'Date et heure de retour',
                'date_widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Mettre à jour",
                'attr' => ['class' => 'primary-button']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tracking::class,
        ]);
    }
}
