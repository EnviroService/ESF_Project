<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class AnonymizationUser
{
    protected $userRepository;
    protected $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function anonymizUser(){
        $date = new \DateTime('now');
        $limitDate = $date->sub(new \DateInterval('P3Y'));
        $users = $this->userRepository->findOldUser($limitDate);
        if (!empty($users)){
            /**
             *@var User $user
             */
            foreach ($users as $user){
                $user
                    ->setBillingAddress('XXX')
                    ->setBillingCity('XXX')
                    ->setBillingPostcode('XXX')
                    ->setEmail('XXX@old.com')
                    ->setCni('XXX')
                    ->setBossName('XXX')
                    ->setErpClient('XXX')
                    ->setJustifyDoc(0)
                    ->setKbis('XXX')
                    ->setOperationalAddress('XXX')
                    ->setOperationalCity('XXX')
                    ->setOperationalPostcode('XXX')
                    ->setRefContact('XXX')
                    ->setRefSign('XXX')
                    ->setUsername('XXX')
                    ->setSIRET('0')
                ;

                $this->em->persist($user);
            }
            $this->em->flush();
            return true;
        }
        return false;

    }
}