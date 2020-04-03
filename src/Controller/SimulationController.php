<?php

namespace App\Controller;

use App\Form\SimulationType;
use App\Repository\RateCardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimulationController extends AbstractController
{
    /**
     * @Route("/simulation", name="simulation")
     * @param Request $request
     * @return array|Response
     */
    public function new() {
        $form = $this->createForm(SimulationType::class);

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
}
