<?php

namespace App\Form;

use App\Repository\RateCardRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimulationType extends AbstractType
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
        }

        $builder
            ->add('country',
                ChoiceType::class, [
                    'choices' => [
                        'France'     => 'France',
                        'Angleterre' => 'Angleterre',
                        'attr' => ['class' => 'form-control']
                    ]
                ])
            ->add('brand',
                ChoiceType::class, [
                    'choices' => $choicesBrand,
                    'placeholder' => 'selectionnez une marque',
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['class' => 'form-control']
                ]
            );

        // Ecoute de la réponse du champ 'marque' après soumission
        $builder
            ->get('brand')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event){
                    $form = $event->getForm();  // On récupère le formulaire pour l'utiliser plus tard
                    $brand = $event->getForm()->getData(); // On récupère les données saisies

                    // Recherche en bdd les modéles associés a la marque sélectionnée
                    $modelListe = $this->repository->getModelByBrand($brand);
                    $choiceModels = [];

                    // Création d'un tableau qui répertorie tous les modéles disponibles
                    foreach ($modelListe as $model){
                        $choiceModels[$model['models']] = $model['models'];
                    }

                    // On crée un champ depuis le FormFactory pour pouvoir ensuite lui ajouter une écoute
                    $builder = $form->getParent()->getConfig()->getFormFactory()->createNamedBuilder(
                        "models",
                        ChoiceType::class,
                        null,
                        [
                        'choices' => $choiceModels,
                        'auto_initialize' => false,
                        'placeholder' => 'selectionnez votre modèle',
                        'attr' => ['class' => 'form-control']
                        ]);

                    // Ajout de l'écoute sur le champ des modéles
                    $builder->addEventListener(
                        FormEvents::POST_SUBMIT,
                        function (FormEvent $event){
                            $model = $event->getForm()->getData();
                            $brand = $event->getForm()->getParent()->get('brand')->getData();

                            // On récupère le téléphone en BDD
                            $tels = $this->repository->findBy([
                                'brand' => $brand,
                                'models' => $model
                            ]);

                            // Proposition de la solution à choisir
                            $solutions = [];
                            foreach ($tels as $tel){
                                $solutions[] = $tel->getSolution();
                            }
                            dump($solutions);
                        }
                    );
                    // On affiche le champ modéle à l'utilisateur
                    $form->getParent()->add($builder->getForm());
                });

        $builder->add('Selectionnez', SubmitType::class);

        /* $builder
         ->add('quantity', NumberType::class)
         ->add('prestation', ChoiceType::class, [
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
//            'data_class' => RateCard::class
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO: Change the autogenerated stub
//        return parent::getBlockPrefix(); // TODO: Change the autogenerated stub
    }
}