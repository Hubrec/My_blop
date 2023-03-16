<?php

namespace App\Controller\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", methods={"GET"})
     */
    public function index(ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('posts page is being accessed');

        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $posts = $postRepository->findAll();

        if (!$posts) {
            throw $this->createNotFoundException(
                'No post found for id '. $id
            );
        }

        return $this -> render('post/index.post.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/new", methods={"GET"})
     */
    public function new(ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('New post page is being accessed');

        $post = new \App\Entity\Post();
        $post->setTitle('I love pandas');
        $post->setDescription('This post is about how much i love pandas');
        $post->setContent('I love pandas so much that i want to eat them, but sometimes when i wake-up in the morning i feel like i want to eat a dog instead.');
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setUpdateAt(new \DateTimeImmutable());
        $post->setPublishedAt(new \DateTimeImmutable());
        $post->setSlug('/post/' . '1');


        $entityManager = $doctrine->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this -> render('post/base.post.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/post/{id}", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('Post/'. $id . ' page is being accessed');

        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '. $id
            );
        }

        return $this -> render('post/base.post.html.twig', [
            'post' => $post,
        ]);
    }
}

?>