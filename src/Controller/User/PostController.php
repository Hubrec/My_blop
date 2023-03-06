<?php

namespace App\Controller\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/post/{name}", methods={"GET"})
     */
    public function index(LoggerInterface $logger, string $name): Response
    {
        $logger->info('Post/'. $name . ' page is being accessed');

        return $this -> render('post/base.post.html.twig', [
            'name' => $name,
        ]);
    }
}

?>