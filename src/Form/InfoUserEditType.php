<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoUserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => "Nom de l'entreprise",
            ])
            ->add('bossName', TextType::class, [
                'required' => true,
                'label' => 'Nom du dirigeant',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Adresse mail',
            ])
            ->add('numPhone', NumberType::class, [
                'required' => true,
                'label' => 'Numéro de téléphone',
            ])
            ->add('operationalAddress', TextType::class, [
                'required' => true,
                'label' => 'Adresse ',
            ])
            ->add('operationalPostcode', TextType::class, [
                'required' => true,
                'label' => 'Code Postal',
            ])
            ->add('operationalCity', TextType::class, [
                'required' => true,
                'label' => 'Ville',
            ])
            ->add('SIRET', NumberType::class, [
                'required' => true,
                'label' => 'Numero de SIRET',
            ])
            ->add('numTVA', NumberType::class, [
                'required' => true,
                'label' => 'Numero de TVA',
            ])
            ->add('billingAddress', TextType::class, [
                'required' => true,
                'label' => 'Adresse de facturation',
            ])
            ->add('billingPostcode', TextType::class, [
                'required' => true,
                'label' => 'Code Postal',
            ])
            ->add('billingCity', TextType::class, [
                'required' => true,
                'label' => 'Ville',
            ])

            ->add('erpClient', TextType::class, [
                'required' => false,
                'label' => 'Numero ERP',
            ])
            ->add('enseigne', EntityType::class, [
                'label' => 'Choisissez une enseigne',
                'class' => 'App\Entity\Enseignes',
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Modifier",
                'attr' => ['class' => 'primary-button']
            ]);


    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
