<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\RateCard;
use App\Entity\Simulation;
use App\Entity\User;
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
        RateCardRepository $rateCards,
        EntityManagerInterface $em
    )
    {
        $user = new User();
        $devis = new Devis();
        $user->setUsername('Utilisateur'); // TO DO : aller chercher le user avec l'authentification
        $devis->setUser($user);
        if($step == 1)
            $rateCards = $rateCards->findAllSolutionDistinct();
        elseif($step == 2)
            $rateCards = $rateCards->findAllBrandDistinct($solution);
        elseif($step == 3)
            $rateCards = $rateCards->findAllModelsDistinct($solution, $brand);

        $form = $this->createForm(SimulationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données postées
            $country = $form->get('country')->getData();
            $prestation = $form->get('prestation')->getData();
            $rateCard = $rateCards->findLineRateCard($solution, $brand, $model, $prestation);
            $simulation = new Simulation();
            $simulation->setRatecard($rateCard)->setDevis($devis);

            return $this->redirectToRoute('simulation-result', [
                'id' => $simulation,
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
     * @Route("/{id}", name="simulation-result")
     * @param Request $request
     * @param Simulation $simulation
     * @return Response
     */
    public function simulationResult(Request $request, Simulation $simulation)
    {
        return $this->render('simulation/simulation.html.twig', [
            'simulation' => $simulation,
        ]);
    }
}
