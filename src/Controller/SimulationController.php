<?php

namespace App\Controller;

use App\Entity\Simulation;
use App\Form\SimulationType;
use App\Repository\RateCardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SimulationController
 * @package App\Controller
 * @Route("/simulation", name="simulation")
 */
class SimulationController extends AbstractController
{
    /**
     * @Route("/", name="new_simulation")
     * @param Request $request
     * @return array|Response
     */
    public function new(Request $request) {
        $simulation = new Simulation();
        $form = $this->createForm(SimulationType::class, $simulation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $simulation = $form->getData();

            return $this->redirectToRoute('task_success');
        }

        /*$result = $rr->createQueryBuilder('u')
            ->orderBy('u.models', 'ASC');
        $listeTel = $result->getQuery()->getResult();

        $listeMarque =[];
        foreach ($listeTel as $tel){
            $marque = $tel->getModels();
            array_push($listeMarque, $marque);
        }
        $result = array_unique($listeMarque);*/

        return $this->render('simulation/simulation.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/list-model", name="list-model")
     */
    public function list_model(RateCardRepository $repository){

    }
}
