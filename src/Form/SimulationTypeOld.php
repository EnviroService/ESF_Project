<?php


namespace App\Form;

use App\Entity\Simulation;
use App\Repository\RateCardRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimulationTypeOld extends AbstractType
{
    /**
     * @var RateCardRepository
     */
    private $repository;

    /**
     * @param RateCardRepository $repository
     */
    public function __construct(RateCardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    )
    {
        $cards = $this->repository->enumerate();

        $choicesBrand = [];
        foreach ($cards as $card) {
            $choicesBrand[$card->getBrand()]  = $card->getBrand();
            //$choicesModel[$card->getBrand()][] = $card->getModels();
            $choicesModel[$card->getModels()] = $card->getModels();
        }

        $builder
            ->add('country',
                ChoiceType::class, [
                'choices' => [
                    'France'     => 'France',
                    'Angleterre' => 'Angleterre'
                ],
                'mapped' => false,
            ])
            ->add('brand',
                ChoiceType::class, [
                'choices' => $choicesBrand,
                'placeholder' => 'selectionnez une marque',
                'required' => false,
                'mapped' => false,
            ]);
        $builder
            ->get('brand')
            ->addEventListener(
        FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                dump($event->getForm());
            }
        );

                $builder->add('submit', SubmitType::class);


            /*->add('model',
                            ChoiceType::class, [
                    'choices' => $choicesModel,
                    'required' => true,
                    'expanded' => false,
                    'attr' => [
                        'class' => 'other-model'
                    ]
                ]);*/

            /*$listeModels = [];
            $i = 0;
            foreach ($choicesModel as $brand) {
                dd($choicesModel);
                    $listeModels[$choicesModel[$i]] = $brand;
                    $builder
                        ->add('model',
                            ChoiceType::class, [
                                'choices' => [
                                    $listeModels[] = $listeModels],
                                'required' => true,
                                'expanded' => true,
                                'attr' => [
                                    'class' => 'other-model'
                                ]
                            ])
                    ;$i++;
            }*/




           /* $builder
            ->add('quantity', NumberType::class)
            ->add('etat_ecran', ChoiceType::class, [
                'choices' => [
                    'ne fonctionne pas' => 'refurb LCD KO',
                    'neuf' => 'neuf',
                    'fonctionnel mais griffé' => 'refurb LCD OK',
                    'le tactile ne fonctionne plus' => 'Repair LCD only'
                ],
                'required' => false
            ])
            ->add('battery', ChoiceType::class, [
                'choices' => [
                    'non' => 'non',
                    'oui' => 'oui'
                ],
                'required' => false
            ])
            ->add('button', ChoiceType::class,[
                'choices' => [
                    'non' => 'non',
                    'oui' => 'oui'
                ],
                'required' => false
            ])
            ->add('empreinte', ChoiceType::class, [
                'choices' => [
                    'non' => 'non',
                    'oui' => 'oui'
                ],
                'required' => false
            ])
            ->add('general', ChoiceType::class, [
                'choices' => [
                    'non' => 'non',
                    'oui' => 'oui',
                ],
                'required' => false
            ]);*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Simulation::class
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO: Change the autogenerated stub
//        return parent::getBlockPrefix(); // TODO: Change the autogenerated stub
    }
}