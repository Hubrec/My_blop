<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

// default controller that manage the home page and the research functionallity
class DefaultController extends AbstractController
{
    // home page
    #[Route(path: '/', name: 'home_page')]
    public function homePage(LoggerInterface $logger): Response
    {
        $logger->info('Home page is being accessed');

        return $this -> render('default/default.base.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    // research functionallity
    #[Route(path: '/research', name: 'research_page')]
    public function researchPage(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Research page is being accessed');

        // check if the fomr is submitted (it can happend everywhere since it's in the navbar)
        if($request->isMethod('POST')) {
            
            $prompt = $request->request->get('search');

            $prompt = strtoupper($prompt);

            if (empty($prompt)) {
                return $this -> render('post/index.post.html.twig', [
                    'posts' => [],
                    'mode' => 1,
                ]);
            }

            $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
            $posts = $postRepository->findAll();

            // filter the posts using 3 criteria: title, content and creator email (case insensitive)
            foreach ($posts as $key => $post) {
                if (strpos(strtoupper($post->getTitle()), $prompt) === false and strpos(strtoupper($post->getContent()), $prompt) === false and strpos(strtoupper($post->getCreator()->getEmail()), $prompt) === false) {
                    unset($posts[$key]);
                }
            }
    
            // sort the posts by update date
            usort($posts, function ($a, $b) {
                return $a->getUpdateAt() <=> $b->getUpdateAt();
            });
            $posts = array_reverse($posts);

            return $this -> render('post/index.post.html.twig', [
                'posts' => $posts,
                'mode' => 1,
            ]);
        }
    }
}

?>