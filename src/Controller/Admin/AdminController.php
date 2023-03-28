<?php

namespace App\Controller\Admin;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;
use App\Entity\Category;
use App\Entity\User;

// administrator controller that manage the admin page
class AdminController extends AbstractController
{
    
    #[Route('/admin', name: 'admin')]
    public function index(ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('I just got the logger');

        // if the user is not logged in, redirect to login page
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // if the user is not an admin, redirect to error page
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->render('admin/error.html.twig', [
                'user' => $this->getUser(),
            ]);
        }

        // get all the posts, categories and users
        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $posts = $postRepository->findAll();

        $categoryRepository = $doctrine->getRepository(\App\Entity\Category::class);
        $categories = $categoryRepository->findAll();

        $userRepository = $doctrine->getRepository(\App\Entity\User::class);
        $users = $userRepository->findAll();


        return $this->render('admin/index.html.twig', [
            'posts' => $posts,
            'categories' => $categories,
            'users' => $users,
        ]);
    }
}

?>