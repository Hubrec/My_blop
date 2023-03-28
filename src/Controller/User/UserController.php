<?php

namespace App\Controller\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\EditUserFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

// user controller that manage the user page
class UserController extends AbstractController
{
    // profile page that show the user profile
    #[Route(path:"/profile", name: 'profile_page')]
    public function profilePage(ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Profile page is being accessed');

        // if the user is not logged in, redirect to login page
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        return $this -> render('user/user.html.twig', [
            'user' => $user,
        ]);
    }

    // edit user page that allow the user to edit his profile
    #[Route(path: '/profile/edit', name: 'user_edit')]
    public function editUser(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Edit user page is being accessed');

        // if the user is not logged in, redirect to login page
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        
        // create the form to edit the profile of the user
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setUserName($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setMood($form->get('mood')->getData());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile_page');
        }


        return $this -> render('user/user.edit.html.twig', [
            'editUserForm' => $form->createView(),
        ]);
    }

    // delete user page that allow the admin to delete a user
    #[Route('/user/{id}/delete', name: 'user_delete')]
    public function deleteUser(ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('Delete user ' . $id . ' page is being accessed');

        // if the user is not logged in, redirect to login page
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        // if the user is not an admin, redirect to home page
        if ($this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            return $this->redirectToRoute('home_page');
        }

        $userRepository = $doctrine->getRepository(\App\Entity\User::class);
        $user = $userRepository->find($id);

        if ($user === null) {
            return $this->redirectToRoute('admin');
        }

        // security check to avoid deleting the admin
        if ($user->getRoles()[0] === 'ROLE_ADMIN') {
            return $this->redirectToRoute('admin');
        }

        // delete all the posts of the user and all the comments of the posts
        if ($user->getPosts() !== null) {
            foreach ($user->getPosts() as $post) {                
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
            }
        }

        // delete all the votes of the user
        if ($user->getVotes() !== null) {
            foreach ($user->getVotes() as $vote) {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($vote);
                $entityManager->flush();
            }
        }

        // delete the user
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

}

?>