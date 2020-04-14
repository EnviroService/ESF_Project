<?php

namespace App\Controller;

use App\Entity\RateCard;
use App\Form\SimuType;
use App\Repository\RateCardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @Route("/simulation")
 */

class SimuController extends AbstractController
{
    /**
     * @Route("/test/", name="simu")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param RateCardRepository $rateCards
     * @return Response
     */
    public function simu(
        Request $request,
        EntityManagerInterface $em,
        RateCardRepository $rateCards
    )
    {
        $rateCard = new RateCard();
        $form = $this->createForm(SimuType::class, $rateCard, [
            'rateCards' => $rateCards
            ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $rateCard = $form->getData();

            $em->persist($rateCard);
            $em->flush();

            return $this->redirectToRoute('simu', [
                'id' => $rateCard->getId(),
            ]);

        }

        return $this->render('simulation/simu.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
