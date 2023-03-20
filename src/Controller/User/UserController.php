<?php

namespace App\Controller\User;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\EditUserFormType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route(path:"/profile", name: 'profile_page')]
    public function profilePage(ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Profile page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        return $this -> render('user/user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/profile/edit', name: 'user_edit')]
    public function editUser(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('Edit user page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        
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

}

?>