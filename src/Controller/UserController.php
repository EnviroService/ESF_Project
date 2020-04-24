<?php

namespace App\Controller;

use App\Entity\Enseignes;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{id}", name="user_show", methods={"GET","POST"})
     * @param User $user
     * @return Response
     */

    public function showUser(User $user): Response
    {

        if ($this->getUser() == $user) {
            $id = $user->getId();
            $enseignes = $user->getEnseigne();

            return $this->render('user/showUser.html.twig', [
                'enseignes' => $enseignes,
                'user' => $user,
                'id' => $id
            ]);
        } else {

            $this->addFlash('danger', 'Vous devez etre connecter pour voir vos informations');
            return $this->redirectToRoute('app_login');
        }
    }


}

