<?php

namespace App\Form;


use App\Entity\User;
use App\Repository\EnseignesRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    /**
     * @var EnseignesRepository
     */
    private $repository;

    /**
     * @param EnseignesRepository $repository
     */
    public function __construct(EnseignesRepository $repository)
    {
        $this->repository = $repository;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enseignes = $this->repository->enumerateEnseignes();

        $choicesEnseignes = [];
        foreach ($enseignes as $enseigne) {
            $choicesEnseignes[$enseigne->getName()]  = $enseigne->getName();
        }


        $builder
            ->add('username', TextType::class, [
                'required' => true
            ])
            ->add('bossName', TextType::class, [
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'required' => true
            ])
            ->add('SIRET', NumberType::class, [
                'required' => true
            ])
            ->add('numTVA', NumberType::class, [
                'required' => true
            ])
            ->add('billingAddress', TextType::class, [
                'required' => true
            ])
            ->add('billingPostcode', TextType::class, [
                'required' => true
            ])
            ->add('billingCity', TextType::class, [
                'required' => true
            ])
            ->add('operationalAddress', TextType::class, [
                'required' => true
            ])
            ->add('operationalPostcode', TextType::class, [
                'required' => true
            ])
            ->add('operationalCity', TextType::class, [
                'required' => true
            ])
            ->add('numPhone', NumberType::class, [
                'required' => true
            ])
            ->add('enseigne', ChoiceType::class, [
                'choices' =>  $choicesEnseignes,
                'label' => 'Choisissez une enseigne',
                'required' => false
            ])
            ->add('kbis', FileType::class, [
                'label' => 'extrait de kbis de moin de 3 mois',
                'required' => true,
                'mapped' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                    ])
                ],
            ])
            ->add('cni', FileType::class, [
                'required' => true,
                'mapped' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                    ])
                ],
            ])
            ->add('password', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmez votre mot de passe'],
                    'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 256,
                    ]),
                ],
            ])
                ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez valider les Conditions Générales',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => User::class,
        ]);
    }
}
