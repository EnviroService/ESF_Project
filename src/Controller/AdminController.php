<?php

namespace App\Controller;

use App\Entity\Options;
use App\Form\OptionsType;
use App\Form\RateCardType;
use App\Repository\RateCardRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/users", name="admin-users")
     */
    public function allowUsers()
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/admin/ratecard", name="admin-ratecard")
     * @param Request $request
     * @param RateCardRepository $rateCards
     * @return Response
     */
    public function uploadRatecard(Request $request, RateCardRepository $rateCards)
    {
        // create form
        $form = $this->createForm(RateCardType::class);
        $form->handleRequest($request);

        // verify data after submission
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $rateCard */
            $rateCard = $form->get('rateCard')->getData();

            // verify extension format
            $ext = $rateCard->getClientOriginalExtension();
            if ($ext != "csv") {
                $this->addFlash('danger', "Le fichier doit être de type .csv. 
                Format actuel envoyé: .$ext");

                return $this->redirectToRoute('admin-ratecard');
            }
            dd($rateCard);
        }

        // find all lines in rateCards
        $rateCards = $rateCards->findAll();

        return $this->render('admin/ratecard.html.twig', [
            'form' => $form->createView(),
            'rateCards' => $rateCards,
        ]);
    }

    /**
     * @Route("/admin/options", name="admin-options")
     * @param Request $request
     * @return Response
     */
    public function uploadOptions(Request $request)
    {
        $options = new Options();
        $form = $this->createForm(OptionsType::class, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $optionsFile */
            $options = $form->get('options')->getData();
            dd($options);
        }
        return $this->render('admin/options.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

