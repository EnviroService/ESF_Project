<?php

namespace App\Controller;

use App\Form\SimulationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimulationController extends AbstractController
{
    /**
     * @Route("/simulation", name="simulation")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request){
        $form = $this->createForm(SimulationType::class);

        return $this->render('simulation/simulation.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
