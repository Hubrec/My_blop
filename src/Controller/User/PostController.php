<?php

namespace App\Controller\User;

use App\Entity\Post;
use App\Form\NewProductFormType;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/post/new', name: 'new_post')]
    public function new(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('New post page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(NewProductFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setTitle($form->get('title')->getData());
            $post->setDescription($form->get('description')->getData());
            $post->setContent($form->get('content')->getData());
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdateAt(new \DateTimeImmutable());
            $post->setPublishedAt(new \DateTimeImmutable());
            $post->setSlug('/post/' . '1');
            $post->setCreator($this->getUser());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/new.post.html.twig', [
            'newPostForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/newTest", methods={"GET"})
     */
    public function newTest(ManagerRegistry $doctrine, LoggerInterface $logger): Response
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

    #[Route('/post/{id}', name: 'post_show')]
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