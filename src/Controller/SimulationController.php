<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Simulation;
use App\Form\SimulationType;
use App\Repository\RateCardRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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
     * @Route("/{step}/{solution}/{brand}/{model}", defaults={"step" = 1, "solution" = "", "brand" = "","model" = ""}, requirements={"step"="[1-4]"}, name="simulation")
     * @param int $step
     * @param string $solution
     * @param string $brand
     * @param string $model
     * @param Request $request
     * @param UserRepository $users
     * @param RateCardRepository $rateCards
     * @param EntityManagerInterface $em
     * @return Response
     * @throws NonUniqueResultException
     */
    public function simulation(
        int $step,
        string $solution,
        string $brand,
        string $model,
        Request $request,
        UserRepository $users,
        RateCardRepository $rateCards,
        EntityManagerInterface $em
    )
    {
        // creation d'un nouveau devis
        $user = $users->findOneBy(['id' => 1]); // TO DO : aller chercher le user avec l'authentification
        $devis = new Devis();
        $devis->setUser($user);
        $em->persist($devis);
        $em->flush();

        if($step == 1)
            $rateCards = $rateCards->findAllSolutionDistinct();
        elseif($step == 2)
            $rateCards = $rateCards->findAllBrandDistinct($solution);
        elseif($step == 3)
            $rateCards = $rateCards->findAllModelsDistinct($solution, $brand);

        $simulation = new Simulation();
        $form = $this->createForm(SimulationType::class, $simulation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données postées
            $country = $form->get('country')->getData();
            $prestation = $form->get('prestation')->getData();
            $simulation = $form->getData();
            $rateCard = $rateCards->findLineRateCard($solution, $brand, $model, $prestation);
            $simulation->setRatecard($rateCard);

            $simulation->setDevis($devis);
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
        ]);
    }

    /**
     * @Route("/result/{id}", name="simulation-result")
     * @param Request $request
     * @param Simulation $simulation
     * @return Response
     */
    public function simulationResult(Request $request, Simulation $simulation)
    {
        $unitPrice = $simulation->getRatecard()->getPriceRateCard();

        return $this->render('simulation/simulationResult.html.twig', [
            'simulation' => $simulation,
            'price' => $unitPrice,
        ]);
    }
}
