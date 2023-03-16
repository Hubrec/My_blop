<?php

namespace App\Controller\Admin;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/{name}", methods={"GET"})
     */
    public function index(LoggerInterface $logger, string $name): Response
    {
        $logger->info('Admin/'. $name . ' page is being accessed');

        return $this -> render('admin/base.admin.html.twig', [
            'name' => $name,
        ]);
    }

    /**
     * @Route("/admin/posts", methods={"GET"})
     */
    public function adminPosts(LoggerInterface $logger): Response
    {
        $logger->info('Admin/posts page is being accessed');

        return $this -> render('admin/base.admin.html.twig', [
            'name' => 'posts',
        ]);
    }

    /**
     * @Route("/admin/categories", methods={"GET"})
     */
    public function adminCategories(LoggerInterface $logger): Response
    {
        $logger->info('Admin/categories page is being accessed');

        return $this -> render('admin/base.admin.html.twig', [
            'name' => 'categories',
        ]);
    }

    /**
     * @Route("/admin/commentaires", methods={"GET"})
     */
    public function adminCommentaires(LoggerInterface $logger): Response
    {
        $logger->info('Admin/commentaires page is being accessed');

        return $this -> render('admin/base.admin.html.twig', [
            'name' => 'commentaires',
        ]);
    }
}

?>