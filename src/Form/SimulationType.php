<?php

namespace App\Form;

use App\Entity\RateCard;
use App\Repository\RateCardRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                    'attr' => ['class' => 'form-control'],
                    'choices' => [
                        'France'     => 'France',
                        'Angleterre' => 'Angleterre',
                    ]
                ])
            ->add('brand',
                ChoiceType::class, [
                    'choices' => $choicesBrand,
                    'required' => false,
                    'label' => 'Sélectionnez une marque:',
                    'attr' => ['class' => 'form-control']
                ]
            );

        // Ecoute de la réponse du champ 'marque' après soumission
        $builder
            ->get('brand')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();  // On récupère le formulaire pour l'utiliser plus tard
                    $brand = $event->getForm()->getData(); // On récupère les données saisies

                    // Recherche en bdd les modéles associés a la marque sélectionnée
                    $modelListe = $this->repository->getModelByBrand($brand);
                    $choiceModels = [];

                    // Création d'un tableau qui répertorie tous les modéles disponibles
                    foreach ($modelListe as $model) {
                        $choiceModels[$model['models']] = $model['models'];
                    }

                    // On crée un champ depuis le FormFactory pour pouvoir ensuite lui ajouter une écoute
                    $modele = $form->getParent()->getConfig()->getFormFactory()->createNamedBuilder(
                        "models",
                        ChoiceType::class,
                        null,
                        [
                            'choices' => $choiceModels,
                            'auto_initialize' => false,
                            'placeholder' => 'selectionnez votre modèle',
                            'attr' => ['class' => 'form-control']
                        ]);

                    // On affiche le champ modéle à l'utilisateur
                    $form->getParent()->add($modele->getForm());

                    // Ajout de l'écoute sur le champ des modéles
                    $modele->addEventListener(
                        FormEvents::POST_SUBMIT,
                        function (FormEvent $event) {
                            $form = $event->getForm();
                            $model = $event->getForm()->getData();
                            $brand = $event->getForm()->getParent()->get('brand')->getData();

                            // On récupère le téléphone en BDD
                            $tels = $this->repository->findBy([
                                'brand' => $brand,
                                'models' => $model
                            ]);

                            // Vérification que modeles a bien été renseigné avant d'afficher le champ solution
                            if ($model != null) {
                                $listSolutions = [];
                                $listPrestation = [];

                                // Récupérer uniquement les solutions concernant l'écran
                                foreach ($tels as $tel) {
                                    $solution = $tel->getSolution();
                                    $prestation = $tel->getPrestation();
                                    $listSolutions[$solution] = $solution;
                                    $listPrestation[$prestation] = $prestation;
                                    /*$verif = str_split(strtolower($solution), 3);
                                    if ($verif[0] == 'lcd'){
                                        $listSolutions[$solution] = $solution;
                                    }*/
                                }
                                $solution = $form->getParent()->getConfig()->getFormFactory()->createNamedBuilder(
                                    'solution',
                                    ChoiceType::class,
                                    null,
                                    [
                                        'choices' => $listSolutions,
                                        'expanded' => true,
                                        'multiple' => true,
                                        'auto_initialize' => false,
                                        'attr' => ['class' => 'form-control']
                                    ]
                                );
                                $form->getParent()->add($solution->getForm());


                                $form->getParent()->add('quantity', NumberType::class, [
                                    // Ajouter les flèches haut et bas pour incrémenter ou décrémenter la quantité
                                    'html5' => true,
                                    // Empéche l'utilisateur d'entrer un chiffre à virgule
                                    'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_DOWN,
                                    // Permet d'arrondir le resultat en int entier
                                    'scale' => 0
                                ]);
                                $form->getParent()->add('prestation', ChoiceType::class, [
                                    'choices' => $listPrestation
                                ]);
                            }
                        });
                });

        //$builder->add('Selectionnez', SubmitType::class);

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