<?php

namespace App\Controller;

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
     * @param RateCardRepository $rateRepo
     * @return array|Response
     */
    public function new(Request $request ,RateCardRepository $rateRepo) {
        $form = $this->createForm(SimulationType::class);

        $form->handleRequest($request);

        /*if ($form->isSubmitted() && $form->isValid()){
            $brand = $_POST['brand'];
            ///$choicesModel = $rateRepo->getModelByBrand($brand);

            return $this->render('simulation/simulation.html.twig',[
                'form' => $form->createView(),
                'brand' => $brand,
                //'models' => $choicesModel
            ]);
        }*/

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
