<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Simulation;
use App\Entity\Tracking;
use App\Form\SimulationType;
use App\Repository\DevisRepository;
use App\Repository\RateCardRepository;
use App\Repository\SimulationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/simulation")
 * @IsGranted("ROLE_USER_VALIDATED")
 */
class SimulationController extends AbstractController
{

    /**
     * @Route("/", name="new_simulation")
     * @param Request $request
     * @param RateCardRepository $rateRepo
     * @param DevisRepository $devisRepo
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(
        Request $request,
        RateCardRepository $rateRepo,
        SimulationRepository $simulationRepo,
        EntityManagerInterface $em
    )
    {
        $user = $this->getUser();
        $bonus = $user->getBonusRateCard();
        // Efface une simulation d'un devis et modifie le devis
        // Si il clique sur la croix X.
        if (isset($_GET['accept']) && $_GET['accept'] == false) {
            // Récupération de l'id de la simulation à effacer.
            $simulationId = $_GET['simulation'];
            if ($simulationId) {
                // Recherche de la simulation à modifier
                $simulation = $simulationRepo->findOneBy([
                    'id' => $simulationId
                ]);

                // On récupére le devis pour comparer et supprimer la bonne simulation
                $devis = $simulation->getDevis();
                $simulation->setDevis(null);
                $devis->removeSimulation($simulation);
                $em->persist($devis);
                $em->persist($simulation);
                $em->flush();
            }

            return $this->redirectToRoute("show_panier", [
                'id' => $user->getId()
            ]);
        }

        $form = $this->createForm(SimulationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $form->get('brand')->getData();
            $model = $form->get('models')->getData();

            if ($model != null) {
                $result = $form->get('models')->getParent()->getData();

                if ($result['solution'] != null) {
                    $solution = $result['solution'];
                    $rate[$solution] = $rateRepo->findBy([
                        'brand' => $brand,
                        'models' => $model,
                        'solution' => $solution
                    ]);

                    if ($result['prestation'] != null) {
                        $nombreTel = $result['quantity'];
                        $prestation = $result['prestation'];
                        $devis = new Devis();
                        $devis->setUser($user);
                        $rate = $rateRepo->findOneBy([
                            'brand' => $brand,
                            'models' => $model,
                            'prestation' => $prestation,
                            'solution' => $solution
                        ]);
                        $simulation = new Simulation();
                        $simulation
                            ->setQuantity($nombreTel)
                            ->setRatecard($rate)
                        ;
                        $em->persist($simulation);
                        $em->flush();
                        $devis->addSimulation($simulation);
                        $em->persist($devis);
                        $price[$solution] = $rate->getPriceRateCard() * $nombreTel * $bonus;
                        $em->flush();

                        return $this->redirectToRoute("show_panier", [
                            'id' => $user->getId()
                        ]);
                    }

                    return $this->render('simulation/simulation.html.twig', [
                        'form' => $form->createView(),
                        'brand' => $brand,
                        'model' => $model,
                        'prestation' => null,
                        'solution' => null
                    ]);
                }
            }

            return $this->render('simulation/simulation.html.twig', [
                'form' => $form->createView(),
                'brand' => $brand,
                'model' => $model,
                'prestation' => null,
                'solution' => null
            ]);
        }

        return $this->render('simulation/simulation.html.twig', [
            'form' => $form->createView(),
            'brand' => null,
            'model' => null,
            'solution' => null,
            'prestation' => null,
        ]);

    $this->addFlash('danger', 'Vous devez vous connecter ou créer un compte pour accéder à cette page');
    return $this->redirectToRoute("app_login");

    }

    /**
     * @Route("/{id}", name="add_simulation")
     * @param Request $request
     * @param Devis $devis
     * @param RateCardRepository $rateRepo
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function addSimulation(Request $request,
                                    Devis $devis,
                                    RateCardRepository $rateRepo,
                                    EntityManagerInterface $em)
    {
        $form = $this->createForm(SimulationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $brand = $form->get('brand')->getData();
            $model = $form->get('models')->getData();

            if ($model != null){
                $result = $form->get('models')->getParent()->getData();

                if ($result['solution'] != null) {
                    $solution = $result['solution'];
                    $rate[$solution] = $rateRepo->findBy([
                        'brand' => $brand,
                        'models' => $model,
                        'solution' => $solution
                    ]);

                    if ($result['prestation'] != null) {
                        $nombreTel = $result['quantity'];
                        $prestation = $result['prestation'];
                        $rate = $rateRepo->findOneBy([
                            'brand' => $brand,
                            'models' => $model,
                            'prestation' => $prestation,
                            'solution' => $solution
                        ]);
                        $simulation = new Simulation();
                        $simulation
                            ->setQuantity($nombreTel)
                            ->setRatecard($rate);
                        $em->persist($simulation);
                        $em->flush();
                        $devis->addSimulation($simulation);
                        $em->persist($devis);
                    }
                    $em->flush();

                    return $this->redirectToRoute("show_panier", ['id' => $devis->getUser()->getId()]);
                }
            }

            return $this->render('simulation/simulation.html.twig', [
                'form' => $form->createView(),
                'brand' => $brand,
                'model' => $model,
                'solution' => null
            ]);
        }

        return $this->render('simulation/simulation.html.twig', [
            'form' => $form->createView(),
            'brand' => null,
            'model' => null,
            'solution' => null
        ]);
    }

    /**
     * @Route("/modif/{id}", name="modif_devis")
     * @param Devis $devis
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function modiDevis(Devis $devis, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $simulations = $devis->getSimulations();
        foreach ($simulations as $simulation){
            $nombreTel = $simulation->getQuantity();
            $bonus = $simulation->getDevis()->getUser()->getBonusRateCard();
            $simulation->setIsValidated(false);
            $em->persist($simulation);
            $price[$simulation->getRatecard()->getSolution()] = $simulation->getRatecard()->getPriceRateCard()  * $nombreTel * $bonus;
        }
        $em->flush();
        //$price[$solution] = $rates[$solution]->getPriceRateCard() * $nombreTel * $bonus;

        return $this->render("simulation/simulationResult.html.twig", [
            'simulations' => $simulations,
            'price' => $price,
            'user' => $user
        ]);
    }
}
