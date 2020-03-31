<?php

namespace App\Controller;

use App\Entity\RateCard;
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
            $rateCardFile = $form->get('rateCard')->getData();
            dd($rateCardFile);
        }

        return $this->render('admin/ratecard.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/options", name="admin-options")
     */
    public function uploadOptions()
    {
        return $this->render('admin/options.html.twig');
    }
}

