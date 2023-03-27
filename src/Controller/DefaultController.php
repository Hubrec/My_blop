<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

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

    #[Route(path: '/research', name: 'research_page')]
    public function researchPage(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Research page is being accessed');

        if($request->isMethod('POST')){
            
            $prompt = $request->request->get('search');

            $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
            $posts = $postRepository->findAll();

            foreach ($posts as $key => $post) {
                if (strpos($post->getTitle(), $prompt) === false and strpos($post->getContent(), $prompt) === false and strpos($post->getCreator()->getEmail(), $prompt) === false) {
                    unset($posts[$key]);
                }
            }
    
            shuffle($posts);

            return $this -> render('post/index.post.html.twig', [
                'posts' => $posts,
            ]);
        }
    }
}

?>