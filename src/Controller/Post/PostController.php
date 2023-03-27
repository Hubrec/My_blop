<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\NewPostFormType;
use App\Form\EditPostFormType;
use App\Form\CommentFormType;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;


class PostController extends AbstractController
{
    #[Route('/posts/mode/{mode}', name: 'posts')]
    public function index(ManagerRegistry $doctrine, LoggerInterface $logger, int $mode): Response
    {
        $logger->info('posts page is being accessed');

        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $posts = $postRepository->findAll();

        if ($mode == 0) {
            shuffle($posts);
        } else if ($mode == 1) {
            usort($posts, function ($a, $b) {
                return $a->getUpdateAt() <=> $b->getUpdateAt();
            });
            $posts = array_reverse($posts);
        } else if ($mode ==2) {
            usort($posts, function ($a, $b) {
                return $a->getUpdateAt() <=> $b->getUpdateAt();
            });
        } else {
            usort($posts, function ($a, $b) {
                return $a->getVotes()->count() <=> $b->getVotes()->count();
            });
            $posts = array_reverse($posts);
        }

        return $this -> render('post/index.post.html.twig', [
            'posts' => $posts,
            'mode' => $mode,
        ]);
    }

    #[Route('/post/new', name: 'new_post')]
    public function new(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('New post page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $options = $doctrine->getRepository(Category::class)->findAll();
        $opt = [];

        foreach ($options as $option) {
            $opt[$option->getId()] = $option->getName();
        }

        $form = $this->createForm(NewPostFormType::class, $opt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = new Post();
            $post->setTitle($form->get('title')->getData());
            $post->setDescription($form->get('description')->getData());
            $post->setContent($form->get('content')->getData());
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdateAt(new \DateTimeImmutable());
            $post->setPublishedAt(new \DateTimeImmutable());
            $post->setSlug('/post/' . '1');
            $post->setCreator($this->getUser());

            for ($i = 0; $i < count($form->get('categories')->getData()); $i++) {
                $cat = $doctrine->getRepository(Category::class)->findByName($form->get('categories')->getData()[$i])[0];
                $post->addCategory($cat);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/new.post.html.twig', [
            'newPostForm' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}', name: 'post_show')]
    public function show(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('Post/'. $id . ' page is being accessed');

        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '. $id
            );
        }

        $votes = 0;
        foreach ($post->getVotes() as $vote) {
            if ($vote->isState()) {
                $votes++;
            } else {
                $votes--;
            }
        }

        $user = $this->getUser();

        if ($user !== null) {
            foreach ($post->getVotes() as $vote) {
                if ($vote->getUser() === $user) {
                    if ($vote->isState()) {
                        $upVoted = true;
                        $downVoted = false;
                    } else {
                        $upVoted = false;
                        $downVoted = true;
                    }
                }
            }
        }

        if (!isset($upVoted)) {
            $upVoted = false;
            $downVoted = false;
        }

        $commentRepository = $doctrine->getRepository(\App\Entity\Comment::class);
        $comments = $commentRepository->findBy(['post' => $post]);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->getUser() === null) {
                return $this->redirectToRoute('app_login');
            }

            $comment->setContent($form->get('content')->getData());
            $comment->setUsername($this->getUser()->getUsername());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setPost($post);
            $comment->setValid(true);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this -> render('post/base.post.html.twig', [
            'post' => $post,
            'user' => $user,
            'comments' => $comments,
            'votes' => $votes,
            'upVoted' => $upVoted,
            'downVoted' => $downVoted,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/edit', name: 'post_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('Post/'. $id . ' edit page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $post = $postRepository->find($id);

        if ($this->getUser()->getId() !== $post->getCreator()->getId() && $this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            return $this->redirectToRoute('post_show', ['id' => $id]);
        }  

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '. $id
            );
        }

        $options = $doctrine->getRepository(Category::class)->findAll();
        $opt = [];

        foreach ($options as $option) {
            $opt[0][$option->getId()] = $option->getName();
        }

        $opt[1] = $post;

        $form = $this->createForm(EditPostFormType::class, $opt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setTitle($form->get('title')->getData());
            $post->setDescription($form->get('description')->getData());
            $post->setContent($form->get('content')->getData());
            $post->setUpdateAt(new \DateTimeImmutable());

            if (sizeof($form->get('categories')->getData()) !== 0) {
                foreach ($post->getCategories() as $category) {
                    $post->removeCategory($category);
                }
                for ($i = 0; $i < count($form->get('categories')->getData()); $i++) {
                    $cat = $doctrine->getRepository(Category::class)->findByName($form->get('categories')->getData()[$i])[0];
                    $post->addCategory($cat);
                }
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        $user = $this->getUser();

        return $this -> render('post/edit.post.html.twig', [
            'post' => $post,
            'editPostForm' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/delete', name: 'post_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('Post/'. $id . ' delete page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $postRepository = $doctrine->getRepository(\App\Entity\Post::class);
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '. $id
            );
        }

        if ($this->getUser()->getId() !== $post->getCreator()->getId() && $this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            return $this->redirectToRoute('post_show', ['id' => $id]);
        }

        if ($post->getComments() !== null) {
            foreach ($post->getComments() as $comment) {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($comment);
                $entityManager->flush();
            }
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('posts');
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete')]
    public function deleteComment(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('Comment/'. $id . ' delete page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $commentRepository = $doctrine->getRepository(\App\Entity\Comment::class);
        $comment = $commentRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comment found for id '. $id
            );
        }

        if ($this->getUser()->getId() !== $comment->getPost()->getCreator()->getId() && $this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
        }        

        $entityManager = $doctrine->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
    }
}

?>