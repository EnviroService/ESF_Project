<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/admin/ratecard", name="admin-ratecard")
     */
    public function ratecard()
    {
        return $this->render('admin/ratecard.html.twig');
    }
}

