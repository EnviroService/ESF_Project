<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // HomePage
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('home/index.html.twig');
    }

    // Page ENVIRO SERVICES FRANCE
    /**
     * @Route("/infos", name="infos")
     */
    public function infos()
    {
        return $this->render('home/infos.html.twig');
    }

    // Page Nos métiers / Nos services
    /**
     * @Route("/services", name="services")
     */
    public function description()
    {
        return $this->render('home/services.html.twig');
    }

    // Page Nos offres
    /**
     * @Route("/offers", name="offers")
     */
    public function offers()
    {
        return $this->render('home/offers.html.twig');
    }

    // Conditions Générales de Vente
    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgv()
    {
        return $this->render('home/cgv.html.twig');
    }

    // Mentions légales
    /**
     * @Route("/mentions", name="legal_notice")
     */
    public function legal_notice()
    {
        return $this->render('home/legal_notice.html.twig');
    }

    // SAV - page à compléter
    /**
     * @Route("/sav", name="SAV")
     */
    public function SAV()
    {
        return $this->render('home/sav.html.twig');
    }

    // Cookies
    /**
     * @Route("/cookies", name="cookies")
     */
    public function cookies()
    {
        return $this->render('home/cookies.html.twig');
    }

    // RGPD
    /**
     * @Route("/rgpd", name="rgpd")
     */
    public function rgpd()
    {
        return $this->render('home/rgpd.html.twig');
    }

}

