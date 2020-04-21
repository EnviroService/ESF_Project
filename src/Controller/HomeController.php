<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/infos", name="infos")
     */
    public function infos()
    {
        return $this->render('home/infos.html.twig');
    }

    /**
     * @Route("/services", name="services")
     */
    public function description()
    {
        return $this->render('home/services.html.twig');
    }

    /**
     * @Route("/offers", name="offers")
     */
    public function offers()
    {
        return $this->render('home/offers.html.twig');
    }

    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgv()
    {
        return $this->render('home/cgv.html.twig');
    }

    /**
     * @Route("/mentions", name="legal_notice")
     */
    public function legal_notice()
    {
        return $this->render('home/legal_notice.html.twig');
    }

    /**
     * @Route("/sav", name="SAV")
     */
    public function SAV()
    {
        return $this->render('home/sav.html.twig');
    }

    /**
     * @Route("/cookies", name="cookies")
     */
    public function cookies()
    {
        return $this->render('home/cookies.html.twig');
    }

    /**
     * @Route("/rgpd", name="rgpd")
     */
    public function rgpd()
    {
        return $this->render('home/rgpd.html.twig');
    }

}

