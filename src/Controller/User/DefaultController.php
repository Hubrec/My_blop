<?php

namespace App\Controller\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/hello", methods={"GET"})
     */
    public function index(LoggerInterface $logger): Response
    {
        $logger->info('Index page is being accessed');

        return $this -> render('default/base.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}

?>