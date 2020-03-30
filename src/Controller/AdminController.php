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
     * @Route("/admin/users", name="admin-users")
     */
    public function allowUsers()
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/admin/ratecard", name="admin-ratecard")
     */
    public function uploadRatecard()
    {
        return $this->render('admin/ratecard.html.twig');
    }

    /**
     * @Route("/admin/options", name="admin-options")
     */
    public function uploadOptions()
    {
        return $this->render('admin/options.html.twig');
    }
}

