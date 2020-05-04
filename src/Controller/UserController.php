<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BookingRepository;
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
     * @IsGranted("ROLE_USER_VALIDATED")
     * @param User $user
     * @param BookingRepository $bookings
     * @return Response
     */

    public function showUser(User $user, BookingRepository $bookings): Response
    {
        $id = $user->getId();
        $enseignes = $user->getEnseigne();
        $bookings = $bookings->findBy(['user'=>$user]);
        return $this->render('user/showUser.html.twig', [
            'enseignes' => $enseignes,
            'user' => $user,
            'id' => $id,
            'bookings' => $bookings,
        ]);
    }
}

