<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Simulation;
use App\Form\SimulationType;
use App\Repository\RateCardRepository;
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
        EntityManagerInterface $em
    )
    {
        $user = $this->getUser();
        if ($this->getUser() != null){
            $role = $user->getRoles();
            if ($user != null && $role[0] == "ROLE_USER_VALIDATED"){
                $form = $this->createForm(SimulationType::class);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $brand = $form->get('brand')->getData();
                    $model = $form->get('models')->getData();

                    if ($model != null){
                        $result = $form->get('models')->getParent()->getData();

                        if ($result['solution'] != null){
                            $solutions = [];
                            $nombreTel = $result['quantity'];
                            foreach ($result['solution'] as $solution){
                                $rate = $rateRepo->findOneBy([
                                    'solution' => $solution
                                ]);
                                dd($rate);
                            }
                         /*
                                $priceEcran = $rateCardEcran->getPriceRateCard() * $nombreTel;
                                $priceBatterie = $rateCardBatterie->getPriceRateCard() * $nombreTel;
                                $price = $priceBatterie + $priceEcran;
                                $devis = new Devis();
                                $devis->setUser($user);
                                $em->persist($devis);
                                $em->flush();
                                $simulation = new Simulation();
                                dump($devis);
                            }*/

                            return $this->render('simulation/simulation.html.twig', [
                                'form' => $form->createView(),
                                'brand' => $brand,
                                'model' => $model,
                                'solution' => $solutions
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

    /*

    /**
     * @Route("/{step}/{solution}/{brand}/{model}/{prestation}", defaults={"step" = 1, "solution" = "", "brand" = "","model" = "","prestation" = ""}, requirements={"step"="[1-5]"}, name="simulation")
     * @IsGranted("ROLE_USER")
     * @param int $step
     * @param string $solution
     * @param string $brand
     * @param string $model
     * @param string $prestation
     * @param Request $request
     * @param UserRepository $users
     * @param RateCardRepository $rateCards
     * @param EntityManagerInterface $em
     * @return Response
     * @throws NonUniqueResultException
     /
    public function simulation(
        int $step,
        string $solution,
        string $brand,
        string $model,
        string $prestation,
        Request $request,
        UserRepository $users,
        RateCardRepository $rateCards,
        EntityManagerInterface $em
    )
    {
        if($step == 1)
            $rateCards = $rateCards->findAllSolutionDistinct();
        elseif($step == 2)
            $rateCards = $rateCards->findAllBrandDistinct($solution);
        elseif($step == 3)
            $rateCards = $rateCards->findAllModelsDistinct($solution, $brand);
        elseif($step == 4)
            $rateCards = $rateCards->findAllPrestationsDistinct($solution, $brand, $model);

        $simulation = new Simulation();
        $form = $this->createForm(SimulationType::class, $simulation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données postées
            $country = $form->get('country')->getData();
            $simulation = $form->getData();
            $rateCard = $rateCards->findLineRateCard($solution, $brand, $model, $prestation);
            $simulation->setRatecard($rateCard);

            $em->persist($simulation);
            $em->flush();

            return $this->redirectToRoute('simulation-result', [
                'id' => $simulation->getId(),
            ]);

        }

        return $this->render('simulation/simulation.html.twig', [
            'form' => $form->createView(),
            'rateCards' => $rateCards,
            'step' => $step,
            'solution' => $solution,
            'brand' => $brand,
            'model' => $model,
            'prestation' => $prestation,
        ]);
    }

    /**
     * @Route("/result/{id}", name="simulation-result")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param Simulation $simulation
     * @return Response
     /
    public function simulationResult(Request $request, Simulation $simulation)
    {
        $unitPrice = $simulation->getRatecard()->getPriceRateCard();

        return $this->render('simulation/simulationResult.html.twig', [
            'simulation' => $simulation,
            'price' => $unitPrice,
        ]);
    }
    */

}
