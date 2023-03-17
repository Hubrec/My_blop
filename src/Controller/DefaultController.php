<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

class DefaultController extends AbstractController
{

    #[Route(path: '/', name: 'home_page')]
    public function homePage(LoggerInterface $logger): Response
    {
        $logger->info('Home page is being accessed');

        return $this -> render('default/default.base.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route(path:"/profile", name: 'profile_page')]
    public function profilePage(ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Profile page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();



        return $this -> render('default/user.html.twig', [
            'user' => $user,
        ]);
    }

}

?>