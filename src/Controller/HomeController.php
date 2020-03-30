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
}

