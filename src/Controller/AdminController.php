<?php

namespace App\Controller;

use App\Entity\Options;
use App\Entity\RateCard;
use App\Form\OptionsType;
use App\Form\RateCardType;
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
     * @return Response
     */
    public function uploadRatecard(Request $request)
    {
        $rateCard = new RateCard();
        $form = $this->createForm(RateCardType::class, $rateCard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $rateCardFile */
            $rateCard = $form->get('rateCard')->getData();
            dd($rateCard);
        }

        return $this->render('admin/ratecard.html.twig', [
            'form' => $form->createView(),
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

