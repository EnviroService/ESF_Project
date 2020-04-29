<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Simulation;
use App\Form\SimulationType;
use App\Repository\DevisRepository;
use App\Repository\RateCardRepository;
use App\Repository\SimulationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/simulation")
 */
class SimulationController extends AbstractController
{

    /**
     * @Route("/", name="new_simulation")
     * @param Request $request
     * @param RateCardRepository $rateRepo
     * @return Response
     */
    public function new(
        Request $request,
        RateCardRepository $rateRepo,
        SimulationRepository $simuRepo,
        DevisRepository $devisRepo,
        EntityManagerInterface $em
    )
    {
        $user = $this->getUser();
        if ($this->getUser() != null){
            $role = $user->getRoles();
            $bonus = $user->getBonusRateCard();
            if ($user != null && $role[0] == "ROLE_USER_VALIDATED"){
                if (isset($_GET['accept']) && $_GET['accept'] == true){
                    $simulationId = $_GET['simulation'];
                    $devisId = $_GET['devis'];
                    $devis = $devisRepo->findOneBy([
                        'id' => $devisId
                    ]);
                    $simulations = $devis->getSimulations();
                    foreach ($simulations as $simu){
                        $simuid = $simu->getId();
                        $solution = $simu->getRatecard()->getSolution();
                        $rate = $simu->getRatecard();
                        if ($simuid == $simulationId){
                            $simu->setIsValidated(true);
                            $em->persist($simu);
                            $em->flush();
                        }
                        $nombreTel = $simu->getQuantity();
                        $price[$solution] = $rate->getPriceRateCard() * $nombreTel * $bonus;
                    }

                    return $this->render('simulation/simulationResult.html.twig', [
                        'simulations' => $simulations,
                        'price' => $price,
                        'devis' => $devis,
                        'user' => $user
                    ]);
                } elseif (isset($_GET['accept']) && $_GET['accept'] == false){
                    $simulationId = $_GET['simulation'];
                    $devisId = $_GET['devis'];
                    $devis = $devisRepo->findOneBy([
                        'id' => $devisId
                    ]);
                    $simulations = $devis->getSimulations();
                    foreach ($simulations as $simu){
                        $simuid = $simu->getId();
                        $solution = $simu->getRatecard()->getSolution();
                        $rate = $simu->getRatecard();
                        if ($simuid == $simulationId){
                            $devis->removeSimulation($simu);
                            $em->persist($simu);
                            $em->flush();
                        }
                        $nombreTel = $simu->getQuantity();
                        $price[$solution] = $rate->getPriceRateCard() * $nombreTel * $bonus;
                    }

                    return $this->render('simulation/simulationResult.html.twig', [
                        'simulations' => $simulations,
                        'price' => $price,
                        'devis' => $devis,
                        'user' => $user
                    ]);
                }
                $form = $this->createForm(SimulationType::class);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $brand = $form->get('brand')->getData();
                    $model = $form->get('models')->getData();

                    if ($model != null){
                        $result = $form->get('models')->getParent()->getData();

                        if ($result['solution'] != null){
                            $nombreTel = $result['quantity'];
                            $prestation = $result['prestation'];
                            $devis = new Devis();
                            $devis->setUser($user);
                            foreach ($result['solution'] as $solution){
                                $rates[$solution] = $rateRepo->findOneBy([
                                    'brand' => $brand,
                                    'models' => $model,
                                    'prestation' => $prestation,
                                    'solution' => $solution
                                ]);
                                $simulation = new Simulation();
                                $simulation
                                    ->setQuantity($nombreTel)
                                    ->setRatecard($rates[$solution]);
                                $em->persist($simulation);
                                $em->flush();
                                $devis->addSimulation($simulation);
                                $em->persist($devis);
                                $price[$solution] = $rates[$solution]->getPriceRateCard() * $nombreTel * $bonus;
                            }
                            $em->flush();
                            $simulations = $devis->getSimulations();

                            $priceTotal = array_sum($price);
                            return $this->render('simulation/simulationResult.html.twig', [
                                'simulations' => $simulations,
                                'devis' => $devis,
                                'priceTotal' => $priceTotal,
                                'price' => $price,
                                'result' => $result
                            ]);
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
                    //'models' => $choicesModel
                ]);
            }
        }

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
    public function validationDevis(Request $request,
                                    Devis $devis,
                                    RateCardRepository $rateRepo,
                                    EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $bonus = $user->getBonusRateCard();
        $form = $this->createForm(SimulationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $brand = $form->get('brand')->getData();
            $model = $form->get('models')->getData();

            if ($model != null){
                $result = $form->get('models')->getParent()->getData();

                if ($result['solution'] != null){
                    $nombreTel = $result['quantity'];
                    $prestation = $result['prestation'];

                    foreach ($result['solution'] as $solution){
                        $rates[$solution] = $rateRepo->findOneBy([
                            'brand' => $brand,
                            'models' => $model,
                            'prestation' => $prestation,
                            'solution' => $solution
                        ]);
                        $simulation = new Simulation();
                        $simulation
                            ->setQuantity($nombreTel)
                            ->setRatecard($rates[$solution]);
                        $em->persist($simulation);
                        $em->flush();
                        $devis->addSimulation($simulation);
                        $em->persist($devis);
                        $price[$solution] = $rates[$solution]->getPriceRateCard() * $nombreTel * $bonus;
                    }
                    $em->flush();
                    $simulations = $devis->getSimulations();

                    $priceTotal = array_sum($price);
                    return $this->render('simulation/simulationResult.html.twig', [
                        'simulations' => $simulations,
                        'devis' => $devis,
                        'priceTotal' => $priceTotal,
                        'price' => $price,
                        'result' => $result,
                        'user' => $user
                    ]);
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
}
