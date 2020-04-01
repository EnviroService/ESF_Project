<?php


namespace App\Form;


use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('society', TextType::class)
           ->add('firstname', TextType::class)
           ->add('lastname', TextType::class)
           ->add('siret', TextType::class)
           ->add('mail', EmailType::class)
           ->add('num_tva', TextType::class)
           ->add('phone_number', TextType::class)
           ->add('message', TextareaType::class)
           ->add('kbis', FileType::class, [
               'required' => true,
               'mapped' => false,
               'data_class' => null
           ])
           ->add('cni', FileType::class, [
               'required' => true,
               'mapped' => false,
               'data_class' => null
           ])
       ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

}
