<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Vote;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

// vote controller that manage the votes on posts
class VoteController extends AbstractController
{
    // upvote function that upvote a post
    #[Route('/post/{id}/upvote', name: 'post_upvote')]
    public function upvote(ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        // check if the user is logged in
        $user = $this->getUser();
        if (!$user instanceof User) {
            $logger->error('User not found');
            return $this->redirectToRoute('app_login');
        }

        $post = $doctrine->getRepository(Post::class)->find($id);

        if (!$post instanceof Post) {
            $logger->error('Post not found');
            return $this->redirectToRoute('post_index');
        }

        // get the vote of the user on the post
        $vote = $doctrine->getRepository(Vote::class)->findOneByUserAndPost([
            'Post' => $post,
            'User' => $user,
        ]);

        // if the vote exist on the related post, change the state of the vote or cancel the vote if the state is the same
        if ($vote instanceof Vote) {
            if ($vote->isState()) {
                $doctrine->getManager()->remove($vote);
                $doctrine->getManager()->flush();
                return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
            } else {
                $vote->setState(true);
            }
        } else {
            $vote = new Vote();
            $vote->setPost($post);
            $vote->setUser($user);
            $vote->setState(true);
        }

        $doctrine->getManager()->persist($vote);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }

    // downvote function that downvote a post
    #[Route('/post/{id}/downvote', name: 'post_downvote')]
    public function downvote(ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        // check if the user is logged in
        $user = $this->getUser();
        if (!$user instanceof User) {
            $logger->error('User not found');
            return $this->redirectToRoute('app_login');
        }

        $post = $doctrine->getRepository(Post::class)->find($id);

        if (!$post instanceof Post) {
            $logger->error('Post not found');
            return $this->redirectToRoute('post_index');
        }

        // get the vote of the user on the post
        $vote = $doctrine->getRepository(Vote::class)->findOneByUserAndPost([
            'Post' => $post,
            'User' => $user
        ]);

        // if the vote exist on the related post, change the state of the vote or cancel the vote if the state is the same
        if ($vote instanceof Vote) {
            if (!$vote->isState()) {
                $doctrine->getManager()->remove($vote);
                $doctrine->getManager()->flush();
                return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
            } else {
                $vote->setState(false);
            }
        } else {
            $vote = new Vote();
            $vote->setPost($post);
            $vote->setUser($user);
            $vote->setState(false);
        }

        $doctrine->getManager()->persist($vote);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }


}